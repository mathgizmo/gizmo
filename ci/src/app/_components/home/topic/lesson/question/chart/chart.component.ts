import {
    Component, Inject, OnInit, OnDestroy, ChangeDetectionStrategy,
    Input, OnChanges, SimpleChanges, ChangeDetectorRef
} from '@angular/core';
import {MatSliderModule} from '@angular/material/slider';

@Component({
    selector: 'chart',
    templateUrl: 'chart.component.html',
    styleUrls: ['chart.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ChartComponent implements OnDestroy, OnChanges, OnInit {

    @Input() question: string;
    @Input() chartHeight: number; // dimension of chart area in px

    private mainColor = '#8ED8DD';
    private selectedColor = '#FFB133';
    private strokeColor = '#FFFFFF';
    private strokeWidth = 1;
    private markDiameter = 3;
    private pointDiameter = 1;

    private dotRadius = 4;

    public type = 1;
    public control = 0; // ?? exception if private
    private valueDisplayChart = 1;
    public valueDisplay = 1;
    public value = 0.50;
    public maxValue = 0;
    public controllbaseValue = 0;
    public startValue = 0;
    public endValue = 1;
    public step = 0.5;
    private marksList: number[] = [0, 0.5, 1];
    private marksLabelsList: number[] = [0, 0.5, 1];

    public accuracyControl = 2; // number of decimals (0 - integer)
    private accuracyChart = 2; // number of decimals (0 - integer)

    private initialized = false;
    private oldQuestion: string;

    public inputValue = 0;
    public stepInput = 0.01;
    public maxInputValue = 1;
    public minInputValue = 0;

    private dotsChartRebuildFunctionId; // id of function which rebuild dots chart
    public dots;

    private sliderChartSelected = 0;

    private selectedDragableElement: any = null;
    private chartElement: any = null;
    private chartValueLabelElement: any = null;

    constructor(private ref: ChangeDetectorRef) {
        this.dots = [];
        if (!this.chartHeight) {
            this.chartHeight = 250;
        }
    }

    ngOnInit() {
        this.setClickPosition = this.setClickPosition.bind(this);
        this.startDrag = this.startDrag.bind(this);
        this.drag = this.drag.bind(this);
        this.endDrag = this.endDrag.bind(this);
    }

    onResize(event) {
        this.destroyDotsChart();
        this.buildChart();
    }

    ngOnDestroy() {
        this.destroyDotsChart();
    }

    ngOnChanges(changes: SimpleChanges) {
        if (this.oldQuestion != this.question) {
            this.oldQuestion = this.question;
            this.initialized = false;
        }
        this.destroyDotsChart();
        const promise = new Promise((resolve) => {
            if (this.control == 1) {
                if (this.valueDisplay == 3) {
                    this.value = this.inputValue * (this.maxValue - this.startValue) + this.startValue;
                } else if (this.valueDisplay == 4) {
                    this.value = (this.inputValue / 100) * (this.maxValue - this.startValue) + this.startValue;
                } else {
                    this.value = this.inputValue;
                }
                if (this.type == 4) {
                    let accuracy = this.accuracyControl;
                    if (this.valueDisplay == 3 || this.valueDisplay == 4) {
                        accuracy = Math.max(accuracy, 5);
                    }
                    // find the closest point
                    let point = this.startValue;
                    let diff = Math.abs(this.value - point);
                    for (let i = this.startValue; i <= this.endValue; i += this.step) {
                        const newdiff = Math.abs(this.value - i);
                        if (newdiff < diff) {
                            diff = newdiff;
                            point = i;
                        }
                    }
                    Math.abs(this.value - this.endValue) < diff ?
                        this.value = this.endValue : this.value = Math.round(point * Math.pow(10,
                        accuracy)) / Math.pow(10, accuracy);
                }
            }
            resolve();
        }).then(() => {
            this.buildChart();
        });
    }

    // function to build charts
    private buildChart() {
        if (!this.initialized) {
            const chart = this.question
                .match(new RegExp(/[^{}]+(?=\}%%)/g));
            if (chart['0'].indexOf('type:') >= 0) {
                this.type = parseFloat(chart['0']
                    .match(new RegExp(/type:([^;]*)(?=(;|$))/g))['0']
                    .replace('type:', ''));
            }
            if (chart['0'].indexOf('value-display-chart:') >= 0) {
                this.valueDisplayChart = +chart['0']
                    .match(new RegExp(/value-display-chart:([^;]*)(?=(;|$))/g))['0']
                    .replace('value-display-chart:', '');
            }
            if (chart['0'].indexOf('value-display:') >= 0) {
                this.valueDisplay = +chart['0']
                    .match(new RegExp(/value-display:([^;]*)(?=(;|$))/g))['0']
                    .replace('value-display:', '');
            }
            if (chart['0'].indexOf('value:') >= 0) {
                this.value = parseFloat(chart['0']
                    .match(new RegExp(/value:([^;]*)(?=(;|$))/g))['0']
                    .replace('value:', ''));
            }
            if (chart['0'].indexOf('step:') >= 0) {
                this.step = parseFloat(chart['0']
                    .match(new RegExp(/step:([^;]*)(?=(;|$))/g))['0']
                    .replace('step:', ''));
                if (this.type == 3) {
                    this.step >= 1
                        ? this.step = Math.round(this.step)
                        : this.step = 1;
                }
                if (this.valueDisplay == 3 || this.valueDisplay == 4) {
                    this.accuracyControl = Math.round(this.maxValue / this.step).toString().length;
                    if (this.valueDisplay == 4) {
                        (this.accuracyControl >= 2)
                            ? this.accuracyControl -= 2
                            : this.accuracyControl = 0;
                    }
                } else {
                    Number.isInteger(this.step) ? this.accuracyControl = 0
                        : this.accuracyControl = (this.step + '').split('.')[1].length;
                }
                if (this.valueDisplayChart == 3 || this.valueDisplayChart == 4) {
                    this.accuracyChart = Math.round(this.maxValue / this.step).toString().length;
                    if (this.valueDisplayChart == 4) {
                        (this.accuracyChart >= 2)
                            ? this.accuracyChart -= 2
                            : this.accuracyChart = 0;
                    }
                } else {
                    Number.isInteger(this.step) ? this.accuracyChart = 0
                        : this.accuracyChart = (this.step + '').split('.')[1].length;
                }
            }
            if (chart['0'].indexOf('accuracy-chart-value:') >= 0) {
                this.accuracyChart = +chart['0']
                    .match(new RegExp(/accuracy-chart-value:([^;]*)(?=(;|$))/g))['0']
                    .replace('accuracy-chart-value:', '');
            }
            if (chart['0'].indexOf('accuracy-control-value:') >= 0) {
                this.accuracyControl = +chart['0']
                    .match(new RegExp(/accuracy-control-value:([^;]*)(?=(;|$))/g))['0']
                    .replace('accuracy-control-value:', '');
            }
            if (this.type == 4) {
                if (chart['0'].indexOf('slider-chart-selected:') >= 0) {
                    this.sliderChartSelected = parseFloat(chart['0']
                        .match(new RegExp(/slider-chart-selected:([^;]*)(?=(;|$))/g))['0']
                        .replace('slider-chart-selected:', ''));
                }
            }
            if (chart['0'].indexOf('start:') >= 0) {
                this.startValue = parseFloat(chart['0']
                    .match(new RegExp(/start:([^;]*)(?=(;|$))/g))['0']
                    .replace('start:', ''));
            } else if (this.type == 4) {
                const marks = chart['0'].match(new RegExp(/marks:([^;]*)(?=(;|$))/g))['0']
                    .replace('marks:', '').split(',');
                this.startValue = +marks[0];
            }
            if (chart['0'].indexOf('end:') >= 0) {
                this.endValue = this.maxValue = parseFloat(chart['0']
                    .match(new RegExp(/end:([^;]*)(?=(;|$))/g))['0']
                    .replace('end:', ''));
            } else if (this.type == 4) {
                const marks = chart['0'].match(new RegExp(/marks:([^;]*)(?=(;|$))/g))['0']
                    .replace('marks:', '').split(',');
                this.endValue = this.maxValue = +marks[marks.length - 1];
            }
            if (chart['0'].indexOf('marks:') >= 0) {
                this.marksLabelsList = chart['0']
                    .match(new RegExp(/marks:([^;]*)(?=(;|$))/g))['0']
                    .replace('marks:', '').split(',');
                // constants used in anonymous function below
                const precision = Math.max(this.accuracyControl, this.accuracyChart);
                const startValue = this.startValue;
                const endValue = this.endValue;
                let calc = false;
                if (chart['0'].match(new RegExp(/calculate:([^;]*)(?=(;|$))/g))['0']
                    .replace('calculate:', '').split(',') == 'true') {
                    calc = true;
                }
                const calculate = calc;
                this.marksList = this.marksLabelsList.map(function (elem) {
                    if (('' + elem).includes('/')) {
                        const values = ('' + elem).split('/').map(Number);
                        elem = (values[0] / values[1]);
                        if (calculate) {
                            elem = elem * (endValue - startValue) + startValue;
                        }
                    } else if (('' + elem).includes('%')) {
                        elem = +('' + elem).replace('%', '') / 100;
                        if (calculate) {
                            elem = elem * (endValue - startValue) + startValue;
                        }
                    }
                    return +(+elem).toFixed(precision);
                });
            }
            if (chart['0'].indexOf('max:') >= 0) {
                this.maxValue = parseFloat(chart['0']
                    .match(new RegExp(/max:([^;]*)(?=(;|$))/g))['0']
                    .replace('max:', ''));
            }
            if (this.valueDisplay == 2 && chart['0'].indexOf('controllbase:') >= 0) {
                this.controllbaseValue = parseFloat(chart['0']
                    .match(new RegExp(/controllbase:([^;]*)(?=(;|$))/g))['0']
                    .replace('controllbase:', ''));
            }
            if (chart['0'].indexOf('main-color:') >= 0) {
                this.mainColor = chart['0']
                    .match(new RegExp(/main-color:([^;]*)(?=(;|$))/g))['0']
                    .replace('main-color:', '');
            } else {
                this.mainColor = '#8ED8DD';
            }
            if (chart['0'].indexOf('selected-color:') >= 0) {
                this.selectedColor = chart['0']
                    .match(new RegExp(/selected-color:([^;]*)(?=(;|$))/g))['0']
                    .replace('selected-color:', '');
            } else {
                this.selectedColor = '#FFB133';
            }
            if (chart['0'].indexOf('stroke-color:') >= 0) {
                this.strokeColor = chart['0']
                    .match(new RegExp(/stroke-color:([^;]*)(?=(;|$))/g))['0']
                    .replace('stroke-color:', '');
            } else {
                this.strokeColor = '#FFFFFF';
            }
            if (chart['0'].indexOf('stroke-width:') >= 0) {
                this.strokeWidth = +chart['0']
                    .match(new RegExp(/stroke-width:([^;]*)(?=(;|$))/g))['0']
                    .replace('stroke-width:', '');
            } else {
                this.strokeWidth = 1;
            }
            if (chart['0'].indexOf('mark-diameter:') >= 0) {
                this.markDiameter = +chart['0']
                    .match(new RegExp(/mark-diameter:([^;]*)(?=(;|$))/g))['0']
                    .replace('mark-diameter:', '');
            } else {
                this.markDiameter = 3;
            }
            if (chart['0'].indexOf('point-diameter:') >= 0) {
                this.pointDiameter = +chart['0']
                    .match(new RegExp(/point-diameter:([^;]*)(?=(;|$))/g))['0']
                    .replace('point-diameter:', '');
            } else {
                this.pointDiameter = 1;
            }
            if (chart['0'].indexOf('control:') >= 0) {
                this.control = +chart['0']
                    .match(new RegExp(/control:([^;]*)(?=(;|$))/g))['0']
                    .replace('control:', '');
            } else {
                this.control = 0;
            }
            if (this.control == 1) {
                if (this.valueDisplay == 3) {
                    this.minInputValue = 0;
                    this.maxInputValue = 1;
                    this.stepInput = Math.round(this.step / (this.maxValue - this.startValue) * 10000) / 10000;
                    if (!this.stepInput) {
                        this.stepInput = this.step / (this.maxValue - this.startValue);
                    }
                    this.inputValue = Math.round((this.value - this.startValue)
                        / (this.maxValue - this.startValue) * 10000) / 10000;
                } else if (this.valueDisplay == 4) {
                    this.minInputValue = 0;
                    this.maxInputValue = 100;
                    this.stepInput = Math.round(this.step / (this.maxValue - this.startValue) * 10000) / 100;
                    if (!this.stepInput) {
                        this.stepInput = this.step / (this.maxValue - this.startValue) * 100;
                    }
                    this.inputValue = Math.round((this.value - this.startValue)
                        / (this.maxValue - this.startValue) * 10000) / 100;
                } else {
                    this.minInputValue = this.startValue;
                    this.maxInputValue = this.maxValue;
                    this.stepInput = this.step;
                    this.inputValue = this.value;
                }
            } else {
                this.minInputValue = this.startValue;
            }
            this.initialized = true;
        }
        if (this.value < this.startValue) {
            this.value = this.startValue;
        }
        if (this.value > this.maxValue) {
            this.value = this.maxValue;
        }
        if (this.type == 3) {
            this.value = Math.round(this.value);
        }
        const valuePercent = this.value / this.maxValue;
        const chartContainer = document.getElementById('chart-container');
        let chartValueLabelFontSize = 16;
        if (window.innerWidth < 450) {
            chartValueLabelFontSize = 11;
        }
        const size = Math.min(this.chartHeight, window.innerWidth * 0.5);
        let chartHtml = '';
        const svgns = 'http://www.w3.org/2000/svg';

        // clear chart
        chartContainer.innerHTML = '';

        switch (this.type) {
            default:
            case 1:
                // Chart (type 1 - rectangle)
                this.chartElement = document.createElementNS(svgns, 'svg');
                this.chartElement.style.height = this.chartElement.style.width = size + 'px';

                const rect2 = document.createElementNS(svgns, 'rect');
                rect2.setAttributeNS(null, 'id', 'rect2');
                rect2.setAttributeNS(null, 'height', '' + size);
                rect2.setAttributeNS(null, 'width', '' + size);
                rect2.setAttributeNS(null, 'style', 'fill: ' + this.mainColor + '; stroke: '
                    + this.strokeColor + '; stroke-width: ' + this.strokeWidth + ';');
                this.chartElement.appendChild(rect2);

                const rect1 = document.createElementNS(svgns, 'rect');
                rect1.setAttributeNS(null, 'id', 'rect1');
                rect1.setAttributeNS(null, 'height', '' + valuePercent * size);
                rect1.setAttributeNS(null, 'width', '' + size);
                rect1.setAttributeNS(null, 'y', '' + (1 - valuePercent) * size);
                rect1.setAttributeNS(null, 'style', 'fill: ' + this.selectedColor + '; stroke: '
                    + this.strokeColor + '; stroke-width: ' + this.strokeWidth + ';');
                rect1.setAttributeNS(null, 'class', 'draggable');
                this.chartElement.appendChild(rect1);

                if (this.valueDisplayChart > 0) {
                    let x, y;
                    x = size / 2;
                    if (this.value < this.maxValue / 10) {
                        y = size - (this.value / this.maxValue * size) - 5;
                    } else {
                        y = size - 0.5 * (this.value / this.maxValue * size) + 5;
                    }
                    this.chartValueLabelElement = document.createElementNS(svgns, 'text');
                    this.chartValueLabelElement.setAttributeNS(null, 'text-anchor', 'middle');
                    this.chartValueLabelElement.setAttributeNS(null, 'x', '' + x);
                    this.chartValueLabelElement.setAttributeNS(null, 'y', '' + y);
                    this.chartValueLabelElement.setAttributeNS(null, 'fill', '' + this.strokeColor);
                    this.chartValueLabelElement.setAttributeNS(null, 'class', 'chart-value-label');
                    this.chartValueLabelElement.setAttributeNS(null, 'style', 'font-size: '
                        + chartValueLabelFontSize + 'px;');
                    switch (this.valueDisplayChart) {
                        default:
                        case 1:
                            this.chartValueLabelElement.innerHTML = this.value.toFixed(this.accuracyChart);
                            break;
                        case 2:
                            this.chartValueLabelElement.innerHTML = this.value.toFixed(this.accuracyChart) + '/' + this.maxValue;
                            break;
                        case 3:
                            this.chartValueLabelElement.innerHTML = (this.value / this.maxValue).toFixed(this.accuracyChart);
                            break;
                        case 4:
                            this.chartValueLabelElement.innerHTML = (this.value / this.maxValue * 100).toFixed(this.accuracyChart) + '%';
                            break;
                    }
                    this.chartElement.appendChild(this.chartValueLabelElement);
                }
                chartContainer.appendChild(this.chartElement);
                this.addDragEvents();
                break;
            case 2:
                // Chart (type 2 - pie)
                const radius = size / 2;
                const angle = 2 * Math.PI * valuePercent;
                const x = radius + radius * Math.sin(angle);
                const y = radius - radius * Math.cos(angle);

                this.chartElement = document.createElementNS(svgns, 'svg');
                this.chartElement.style.height = this.chartElement.style.width = size + 'px';

                if (valuePercent <= 0.999) {
                    const circle2 = document.createElementNS(svgns, 'circle');
                    circle2.setAttributeNS(null, 'id', 'circle2');
                    circle2.setAttributeNS(null, 'r', '' + radius);
                    circle2.setAttributeNS(null, 'cx', '' + radius);
                    circle2.setAttributeNS(null, 'cy', '' + radius);
                    circle2.setAttributeNS(null, 'style', 'fill: ' + this.mainColor + '; stroke: ' +
                        this.strokeColor + '; stroke-width: ' + this.strokeWidth + ';');
                    this.chartElement.appendChild(circle2);

                    const circle1 = document.createElementNS(svgns, 'path');
                    circle1.setAttributeNS(null, 'id', 'circle1');
                    circle1.setAttributeNS(null, 'class', 'draggable');
                    let d = 'M' + radius + ',' + radius
                        + ' L' + radius + ',0 A' + radius + ',' + radius;
                    valuePercent <= 0.5 ? d += ' 1 0,1' : d += ' 1 1,1';
                    d += ' ' + x + ', ' + y + ' z';
                    circle1.setAttributeNS(null, 'd', d);
                    circle1.setAttributeNS(null, 'style', 'fill: ' + this.selectedColor + '; stroke: ' +
                        this.strokeColor + '; stroke-width: ' + this.strokeWidth + ';');
                    this.chartElement.appendChild(circle1);
                } else {
                    const circle1 = document.createElementNS(svgns, 'circle');
                    circle1.setAttributeNS(null, 'id', 'circle1');
                    circle1.setAttributeNS(null, 'r', '' + radius);
                    circle1.setAttributeNS(null, 'cx', '' + radius);
                    circle1.setAttributeNS(null, 'cy', '' + radius);
                    circle1.setAttributeNS(null, 'style', 'fill: ' + this.selectedColor + '; stroke: ' +
                        this.strokeColor + '; stroke-width: ' + this.strokeWidth + ';');
                    this.chartElement.appendChild(circle1);
                }

                if (this.valueDisplayChart > 0) {
                    this.chartValueLabelElement = document.createElement('label');
                    this.chartValueLabelElement.classList.add('chart-value-label');
                    this.chartValueLabelElement.style.position = 'absolute';
                    this.chartValueLabelElement.style.color = this.strokeColor;
                    this.chartValueLabelElement.style.fontSize = chartValueLabelFontSize + 'px';
                    if (this.value / this.maxValue < 0.5) {
                        this.chartValueLabelElement.style.left = ((chartContainer.clientWidth
                            - size) / 2 + 8 + x) + 'px';
                    } else {
                        this.chartValueLabelElement.style.right = ((chartContainer.clientWidth
                            - size) / 2 + (+size + 8) - x) + 'px';
                    }
                    if (this.value / this.maxValue < 0.25 || this.value / this.maxValue > 0.75) {
                        this.chartValueLabelElement.style.top = y - chartValueLabelFontSize + 'px';
                    } else {
                        this.chartValueLabelElement.style.top = y + 'px';
                    }
                    if (this.valueDisplayChart == 1) {
                        this.chartValueLabelElement.innerHTML += this.value.toFixed(this.accuracyChart);
                    } else if (this.valueDisplayChart == 2) {
                        this.chartValueLabelElement.innerHTML += this.value.toFixed(this.accuracyChart)
                            + '/' + this.maxValue;
                    } else if (this.valueDisplayChart == 3) {
                        this.chartValueLabelElement.innerHTML += (this.value / this.maxValue)
                            .toFixed(this.accuracyChart);
                    } else if (this.valueDisplayChart == 4) {
                        this.chartValueLabelElement.innerHTML += (this.value / this.maxValue * 100)
                            .toFixed(this.accuracyChart) + '%';
                    }
                    chartContainer.appendChild(this.chartValueLabelElement);
                }
                chartContainer.appendChild(this.chartElement);
                this.addDragEvents();
                break;
            case 3:
                // Chart (type 3 - dots)
                this.chartElement = document.createElement('canvas');
                requestAnimationFrame(() => {
                    // clear chart
                    chartContainer.innerHTML = '';
                    chartContainer.appendChild(this.chartElement);
                    this.chartValueLabelElement = document.createElement('label');
                    this.chartValueLabelElement.classList.add('chart-value-label');
                    this.chartValueLabelElement.style.color = this.strokeColor;
                    this.chartValueLabelElement.style.fontSize = chartValueLabelFontSize + 'px';
                    if (this.valueDisplayChart > 0) {
                        if (this.valueDisplayChart == 1) {
                            this.chartValueLabelElement.innerHTML = '' + this.value.toFixed(this.accuracyChart);
                        } else if (this.valueDisplayChart == 2) {
                            this.chartValueLabelElement.innerHTML = '' + this.value.toFixed(this.accuracyChart) + '/' + this.maxValue;
                        } else if (this.valueDisplayChart == 3) {
                            this.chartValueLabelElement.innerHTML = '' + (this.value / this.maxValue).toFixed(this.accuracyChart);
                        } else if (this.valueDisplayChart == 4) {
                            this.chartValueLabelElement.innerHTML = '' + (this.value / this.maxValue * 100)
                                .toFixed(this.accuracyChart) + '%';
                        }
                        this.chartValueLabelElement.innerHTML += '<br/><br/>';
                    }
                    let dotStyle = 'display: inline-block; border-radius: 50%; height: 15px; width: 15px; border-style: solid; border-width: 1.5px; border-color: ' + this.strokeColor + ';';
                    this.chartValueLabelElement.innerHTML += '<span style="' + dotStyle + ' background-color: ' + this.selectedColor + ';"></span> = ' + this.value.toFixed(0) + '; ';
                    this.chartValueLabelElement.innerHTML += '<span style="' + dotStyle + ' background-color: ' + this.mainColor + ';"></span> = ' + (this.maxValue - this.value).toFixed(0);
                    chartContainer.appendChild(this.chartValueLabelElement);
                });
                this.chartElement.style.height = this.chartHeight + 'px';
                this.chartElement.style.width = this.chartHeight * 2 + 'px';
                this.chartElement.style.maxWidth = '100%';
                this.chartElement.style.maxHeight = window.innerWidth * 0.5 + 'px';

                const ctx = this.chartElement.getContext('2d');
                for (let i = 0; i < this.maxValue; i++) {
                    if (this.dots[i] == undefined) {
                        this.dots[i] = {
                            x: Math.random() * (this.chartElement.width - this.dotRadius * 2),
                            y: Math.random() * (this.chartElement.height - this.dotRadius * 2),
                            radius: this.dotRadius
                        };
                    }
                }
                this.dotsChartRebuildFunctionId = setInterval(() => {
                    ctx.clearRect(0, 0, this.chartElement.width, this.chartElement.height);
                    this.drawDotsChart(this.value, this.maxValue, ctx, this.chartElement);
                }, 80);
                break;
            case 4:
                // Chart (type 4 - slider)
                const maxPointsNumber = 100;
                const hidePoints = ((this.endValue - this.startValue) / this.step > maxPointsNumber) ? true : false;
                const precision = Math.max(this.accuracyControl, this.accuracyChart);
                const padding = 20;
                let width = chartContainer.offsetWidth;
                const indentation = this.pointDiameter + padding;
                this.chartElement = document.createElementNS(svgns, 'svg');
                this.chartElement.style.height = '50px';
                this.chartElement.style.width = width + 'px';
                this.chartElement.style.overflow = 'visible';
                const line = document.createElementNS(svgns, 'line');
                line.setAttributeNS(null, 'x1', indentation + '');
                line.setAttributeNS(null, 'x2', (width - indentation) + '');
                line.setAttributeNS(null, 'y1', '25');
                line.setAttributeNS(null, 'y2', '25');
                line.setAttributeNS(null, 'style', 'stroke:' + this.mainColor
                    + '; stroke-width:' + this.strokeWidth + ';');
                this.chartElement.appendChild(line);
                width -= indentation * 2;
                const currentPointX = (this.value - this.startValue) / (this.endValue
                    - this.startValue) * width + indentation;
                if (this.sliderChartSelected == 1) {
                    const selectedLine = document.createElementNS(svgns, 'line');
                    selectedLine.setAttributeNS(null, 'x1', indentation + '');
                    selectedLine.setAttributeNS(null, 'x2', currentPointX + '');
                    selectedLine.setAttributeNS(null, 'y1', '25');
                    selectedLine.setAttributeNS(null, 'y2', '25');
                    selectedLine.setAttributeNS(null, 'style', 'stroke:' + this.selectedColor
                        + '; stroke-width:' + (this.strokeWidth + 2) + ';');
                    this.chartElement.appendChild(selectedLine);
                }
                for (let i = 0; i < (this.endValue - this.startValue); i += this.step) {
                    const position = (i * width / (this.endValue - this.startValue)) + indentation;
                    const point = Number((i + this.startValue).toFixed(precision));
                    if (this.marksList.includes(point)) {
                        const label = this.marksLabelsList[this.marksList.indexOf(point)];
                        const pointMark = document.createElementNS(svgns, 'circle');
                        pointMark.setAttributeNS(null, 'cx', position + '');
                        pointMark.setAttributeNS(null, 'cy', '25');
                        pointMark.setAttributeNS(null, 'r', (this.markDiameter / 2) + '');
                        pointMark.setAttributeNS(null, 'fill', this.strokeColor);
                        this.chartElement.appendChild(pointMark);
                        const textPosition = ((point - this.startValue) / (this.endValue
                            - this.startValue) * width + indentation);
                        const pointText = document.createElementNS(svgns, 'text');
                        pointText.setAttributeNS(null, 'y', '50');
                        pointText.setAttributeNS(null, 'fill', this.strokeColor);
                        pointText.setAttributeNS(null, 'style', 'font-size: ' + chartValueLabelFontSize + 'px;');
                        pointText.innerHTML = label + '';
                        if (i == 0) {
                            pointText.setAttributeNS(null, 'x', padding + '');
                            pointText.setAttributeNS(null, 'text-anchor', 'start');
                        } else if (i < (this.endValue - this.startValue) - this.step) {
                            pointText.setAttributeNS(null, 'x', textPosition + '');
                            pointText.setAttributeNS(null, 'text-anchor', 'middle');
                        }
                        this.chartElement.appendChild(pointText);
                    } else {
                        if (!hidePoints && !(this.sliderChartSelected == 1)) {
                            const point = document.createElementNS(svgns, 'circle');
                            point.setAttributeNS(null, 'cx', position + '');
                            point.setAttributeNS(null, 'cy', '25');
                            point.setAttributeNS(null, 'r', (this.pointDiameter / 2) + '');
                            point.setAttributeNS(null, 'fill', this.strokeColor);
                            this.chartElement.appendChild(point);
                        }
                    }
                }
                const lastPoint = document.createElementNS(svgns, 'circle');
                lastPoint.setAttributeNS(null, 'cx', (width + indentation) + '');
                lastPoint.setAttributeNS(null, 'cy', '25');
                lastPoint.setAttributeNS(null, 'r', (this.markDiameter / 2) + '');
                lastPoint.setAttributeNS(null, 'fill', this.strokeColor);
                this.chartElement.appendChild(lastPoint);
                if (this.marksList[this.marksList.length - 1].toFixed(precision)
                    == this.endValue.toFixed(precision)) {
                    const lastPointText = document.createElementNS(svgns, 'text');
                    lastPointText.setAttributeNS(null, 'x', (width + indentation + (this.markDiameter / 2)) + '');
                    lastPointText.setAttributeNS(null, 'y', '50');
                    lastPointText.setAttributeNS(null, 'fill', this.strokeColor);
                    lastPointText.setAttributeNS(null, 'style', 'font-size: ' + chartValueLabelFontSize + 'px;');
                    lastPointText.setAttributeNS(null, 'text-anchor', 'end');
                    lastPointText.innerHTML = this.marksLabelsList[this.marksList.length - 1] + '';
                    this.chartElement.appendChild(lastPointText);
                }
                const currentPoint = document.createElementNS(svgns, 'circle');
                currentPoint.classList.add('draggable');
                currentPoint.setAttributeNS(null, 'cx', currentPointX + '');
                currentPoint.setAttributeNS(null, 'cy', '25');
                currentPoint.setAttributeNS(null, 'r', this.markDiameter + '');
                currentPoint.setAttributeNS(null, 'fill', this.selectedColor);
                this.chartElement.appendChild(currentPoint);
                if (this.valueDisplayChart > 0) {
                    const currentValueFixed = Number((this.value).toFixed(precision));
                    const startValueFixed = Number((this.startValue).toFixed(precision));
                    const endValueFixed = Number((this.endValue).toFixed(precision));
                    this.chartValueLabelElement = document.createElementNS(svgns, 'text');
                    if (currentValueFixed == startValueFixed) {
                        this.chartValueLabelElement.setAttributeNS(null, 'x', indentation - (this.markDiameter / 2) + '');
                        this.chartValueLabelElement.setAttributeNS(null, 'text-anchor', 'start');
                    } else if (currentValueFixed == endValueFixed) {
                        this.chartValueLabelElement.setAttributeNS(null, 'x', (width + indentation * 2 - (this.markDiameter / 2)) + '');
                        this.chartValueLabelElement.setAttributeNS(null, 'text-anchor', 'end');
                    } else {
                        this.chartValueLabelElement.setAttributeNS(null, 'x', currentPointX + '');
                        this.chartValueLabelElement.setAttributeNS(null, 'text-anchor', 'middle');
                    }
                    this.chartValueLabelElement.setAttributeNS(null, 'y', '15');
                    this.chartValueLabelElement.setAttributeNS(null, 'fill', this.strokeColor);
                    this.chartValueLabelElement.setAttributeNS(null, 'style', 'font-size: ' + chartValueLabelFontSize + 'px;');
                    this.chartValueLabelElement.classList.add('chart-value-label');
                    if (this.valueDisplayChart == 1) {
                        this.chartValueLabelElement.innerHTML = this.value.toFixed(this.accuracyChart);
                    } else if (this.valueDisplayChart == 2) {
                        this.chartValueLabelElement.innerHTML = this.value.toFixed(this.accuracyChart) + '/' + this.maxValue;
                    } else if (this.valueDisplayChart == 3) {
                        this.chartValueLabelElement.innerHTML = ((this.value - this.startValue)
                            / (this.maxValue - this.startValue)).toFixed(this.accuracyChart);
                    } else if (this.valueDisplayChart == 4) {
                        this.chartValueLabelElement.innerHTML = ((this.value - this.startValue)
                            / (this.maxValue - this.startValue) * 100).toFixed(this.accuracyChart) + '%';
                    }
                    this.chartElement.appendChild(this.chartValueLabelElement);
                }
                chartContainer.appendChild(this.chartElement);
                this.addDragEvents();
                break;
        }
        this.ref.detectChanges();
    }

    // function to draw Dots Chart
    private drawDotsChart(dotsNum: number,
                          maxDotsNum: number, ctx: CanvasRenderingContext2D,
                          canvas: HTMLCanvasElement) {
        for (let i = 0; i < dotsNum; i++) {
            this.dots[i] = this.drawDot(2, ctx, canvas, this.dots[i]);
        }
        for (let i = dotsNum; i < maxDotsNum; i++) {
            this.dots[i] = this.drawDot(1, ctx, canvas, this.dots[i]);
        }
    }

    // function to draw one Dot
    private drawDot(type: number, ctx: CanvasRenderingContext2D,
                    canvas: HTMLCanvasElement, dot) {
        ctx.strokeStyle = this.strokeColor;
        ctx.lineWidth = this.strokeWidth;
        if (type == 1) {
            ctx.fillStyle = this.mainColor;
        }
        if (type == 2) {
            ctx.fillStyle = this.selectedColor;
        }
        dot.x += Math.random() * 2 - 1;
        dot.y += Math.random() * 2 - 1;

        // Check if dot goes beyond the field
        const dotDiameter = dot.radius * 2;
        if (dot.x > canvas.width - dotDiameter) {
            dot.x = canvas.width - dotDiameter;
        }
        if (dot.x < dotDiameter) {
            dot.x = dotDiameter;
        }
        if (dot.y > canvas.height - dotDiameter) {
            dot.y = canvas.height - dotDiameter;
        }
        if (dot.y < dotDiameter) {
            dot.y = dotDiameter;
        }

        ctx.beginPath();
        ctx.arc(dot.x, dot.y, dot.radius, 0, Math.PI * 2, true);
        ctx.fill();
        ctx.stroke();
        return dot;
    }

    // remove dot chart rebuild function if it exists
    private destroyDotsChart() {
        if (this.dotsChartRebuildFunctionId) {
            clearInterval(this.dotsChartRebuildFunctionId);
        }
    }

    private addDragEvents() {
        // change value on click
        this.chartElement.addEventListener('click', this.setClickPosition);
        // drag chart with mouse
        this.chartElement.addEventListener('mousedown', this.startDrag);
        this.chartElement.addEventListener('mousemove', this.drag);
        this.chartElement.addEventListener('mouseup', this.endDrag);
        this.chartElement.addEventListener('mouseleave', this.endDrag);
        // drag chart with touch
        this.chartElement.addEventListener('touchstart', this.startDrag);
        this.chartElement.addEventListener('touchmove', this.drag);
        this.chartElement.addEventListener('touchend', this.endDrag);
        this.chartElement.addEventListener('touchleave', this.endDrag);
        this.chartElement.addEventListener('touchcancel', this.endDrag);
    }

    // drag animation
    private startDrag(event) {
        // if (event.target.classList.contains('draggable')) {
        this.selectedDragableElement = event.target;
        // }
    }

    private drag(event) {
        if (this.selectedDragableElement) {
            this.setClickPosition(event);
        }
    }

    private endDrag(event) {
        this.selectedDragableElement = null;
    }

    // function to set value by clicking on top slider
    private setClickPosition(event) {
        const chartContainer = document.getElementById('chart-container');
        const pos = getAbsolutePosition(chartContainer);

        let accuracy = this.accuracyControl;
        if (this.valueDisplay == 3 || this.valueDisplay == 4) {
            accuracy = Math.max(accuracy, 5);
        }

        const pageX = event.pageX ? event.pageX : event.touches[0].pageX;
        const pageY = event.pageY ? event.pageY : event.touches[0].pageY;
        const x = pageX - pos.x;
        const y = pageY - pos.y;

        if (this.type == 1) {
            const height = chartContainer.offsetHeight;
            this.value = this.maxValue - y * this.maxValue / height;

            // find the closest point
            let point = 0;
            let diff = Math.abs(this.value - point);
            for (let i = 0; i <= this.maxValue; i += this.step) {
                const newdiff = Math.abs(this.value - i);
                if (newdiff < diff) {
                    diff = newdiff;
                    point = i;
                }
            }
            Math.abs(this.value - this.maxValue) < diff ?
                this.value = this.maxValue : this.value = Math.round(point * Math.pow(10,
                accuracy)) / Math.pow(10, accuracy);
        } else if (this.type == 2) {
            const height = chartContainer.offsetHeight;
            const width = chartContainer.offsetWidth;
            const x0 = width / 2;
            const y0 = height / 2;
            const xc = x - x0;
            const yc = y0 - y;
            const angle = Math.atan(xc / yc);
            this.value = angle / (2 * Math.PI) * this.maxValue; // I
            if ((xc > 0 && yc < 0) || (xc <= 0 && yc < 0)) { // II-III
                this.value += 0.5 * this.maxValue;
            } else if (xc < 0 && yc >= 0) { // IV
                this.value += this.maxValue;
            }

            // find the closest point
            let point = 0;
            let diff = Math.abs(this.value - point);
            for (let i = 0; i <= this.maxValue; i += this.step) {
                const newdiff = Math.abs(this.value - i);
                if (newdiff < diff) {
                    diff = newdiff;
                    point = i;
                }
            }
            Math.abs(this.value - this.maxValue) < diff ?
                this.value = this.maxValue : this.value = Math.round(point * Math.pow(10,
                accuracy)) / Math.pow(10, accuracy);
        } else if (this.type == 4) {
            const circleDiameter = 2 * this.dotRadius;
            const indentation = circleDiameter + 5;
            const width = chartContainer.offsetWidth - indentation * 2;
            this.value = (x - indentation) * (this.endValue
                - this.startValue) / width + this.startValue;

            // find the closest point
            let point = this.startValue;
            let diff = Math.abs(this.value - point);
            for (let i = this.startValue; i <= this.endValue; i += this.step) {
                const newdiff = Math.abs(this.value - i);
                if (newdiff < diff) {
                    diff = newdiff;
                    point = i;
                }
            }
            Math.abs(this.value - this.endValue) < diff ?
                this.value = this.endValue : this.value = Math.round(point * Math.pow(10,
                accuracy)) / Math.pow(10, accuracy);

            if (this.value < this.startValue) {
                this.value = this.startValue;
            } else if (this.value > this.endValue) {
                this.value = this.endValue;
            }
        }
        if (this.type == 1 || this.type == 2 || this.type == 4) {
            const promise = new Promise((resolve) => {
                if (this.control == 1 && this.valueDisplay == 3) {
                    this.inputValue = Math.round((this.value - this.startValue)
                        / (this.maxValue - this.startValue) * Math.pow(10, accuracy)) / Math.pow(10, accuracy);
                } else if (this.control == 1 && this.valueDisplay == 4) {
                    this.inputValue = Math.round((this.value - this.startValue)
                        / (this.maxValue - this.startValue) * Math.pow(10, accuracy + 2)) / Math.pow(10, accuracy);
                } else {
                    this.inputValue = Math.round(this.value * Math.pow(10,
                        accuracy)) / Math.pow(10, accuracy);
                }
                resolve();
            }).then(() => {
                this.buildChart();
            });
        }
    }

}

// function to get absolute position of HTML element
function getAbsolutePosition(element) {
    const r = {x: element.offsetLeft, y: element.offsetTop};
    if (element.offsetParent) {
        const tmp = getAbsolutePosition(element.offsetParent);
        r.x += tmp.x;
        r.y += tmp.y;
    }
    return r;
}
