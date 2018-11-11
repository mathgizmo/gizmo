import { Component, Inject, OnInit, OnDestroy, ChangeDetectionStrategy,
    Input, OnChanges, SimpleChanges, ChangeDetectorRef } from '@angular/core';
import {MatSliderModule} from '@angular/material/slider';

@Component({
    selector: 'chart',
    templateUrl: 'chart.component.html' ,
    styleUrls: ['chart.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class ChartComponent implements OnDestroy, OnChanges, OnInit {

    @Input() question: string;
    @Input() chartHeight: number; // dimension of chart area in px

    private mainColor: string = "#8ED8DD";
    private selectedColor: string = "#FFB133";
    private strokeColor: string = "#FFFFFF";
    private strokeWidth: number = 1;
    private markDiameter: number = 3;
    private pointDiameter: number = 1;

    private dotRadius: number = 4;

    private type: number = 1;
    public control: number = 0; // ?? exception if private
    private valueDisplayChart: number = 1;
    private valueDisplay: number = 1;
    private value: number = 0.50;
    private maxValue: number = 0;
    private controllbaseValue: number = 0;
    private startValue = 0;
    private endValue = 1;
    private step: number = 0.5;
    private marksList: number[]= [0, 0.5, 1];
    private marksLabelsList: number[]= [0, 0.5, 1];

    private accuracyControl: number = 2; // number of decimals (0 - integer)
    private accuracyChart: number = 2; // number of decimals (0 - integer)

    private initialized = false;
    private oldQuestion: string;

    private inputValue: number = 0;
    private stepInput: number = 0.01;
    private maxInputValue: number = 1;
    private minInputValue: number = 0;

    private dotsChartRebuildFunctionId; // id of function which rebuild dots chart
    private dots;

    constructor(private ref:ChangeDetectorRef){
      this.dots = [];
      if(!this.chartHeight)
        this.chartHeight=250;
    }

    ngOnInit() {
      document.getElementById('chart-container')
        .addEventListener('click', this.setClickPosition.bind(this));
    }

    onResize(event) {
      this.destroyDotsChart();
      this.buildChart();
    }

    ngOnDestroy() {
      this.destroyDotsChart();
      document.getElementById('chart-container')
        .removeEventListener('click', this.setClickPosition.bind(this));
    }

    ngOnChanges(changes: SimpleChanges) {
        if (this.oldQuestion != this.question) {
            this.oldQuestion = this.question;
            this.initialized = false;
        }
        this.destroyDotsChart();
        let promise = new Promise((resolve) => {
          if(this.control == 1) {
            if (this.valueDisplay == 3) {
              this.value = this.inputValue*(this.maxValue-this.startValue)+this.startValue;
            } else if(this.valueDisplay == 4){
              this.value = (this.inputValue/100)*(this.maxValue-this.startValue)+this.startValue;
            } else {
              this.value = this.inputValue;
            }
            if(this.type == 4) {
              let accuracy = this.accuracyControl;
              if(this.valueDisplay == 3 || this.valueDisplay == 4) {
                accuracy = Math.max(accuracy, 5);
              }
              // find the closest point
              let point = this.startValue;
              let diff = Math.abs(this.value - point);
              for (let i = this.startValue; i <= this.endValue; i+= this.step) {
                let newdiff = Math.abs(this.value - i);
                if (newdiff < diff) {
                  diff = newdiff;
                  point = i;
                }
              }
              Math.abs(this.value - this.endValue) < diff ?
                this.value = this.endValue : this.value = Math.round(point*Math.pow(10,
                  accuracy))/Math.pow(10, accuracy);
            }
          }
          resolve();
        }).then(() => {
          this.buildChart();
        });
    }

    // function to build charts
    private buildChart() {
      if ( !this.initialized ) {
        let chart = this.question
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
          if(this.type == 3) {
            this.step >= 1
              ? this.step = Math.round(this.step)
              : this.step = 1;
          }
          if(this.valueDisplay == 3 || this.valueDisplay == 4) {
            this.accuracyControl = Math.round(this.maxValue/this.step).toString().length;
            if(this.valueDisplay == 4) {
              (this.accuracyControl >= 2)
                ? this.accuracyControl -= 2
                : this.accuracyControl = 0;
            }
          } else {
            Number.isInteger(this.step) ? this.accuracyControl = 0
            : this.accuracyControl = (this.step + "").split(".")[1].length;
          }
          if(this.valueDisplayChart == 3 || this.valueDisplayChart == 4) {
            this.accuracyChart = Math.round(this.maxValue/this.step).toString().length;
            if(this.valueDisplayChart == 4) {
              (this.accuracyChart >= 2)
                ? this.accuracyChart -= 2
                : this.accuracyChart = 0;
            }
          } else {
            Number.isInteger(this.step) ? this.accuracyChart = 0
            : this.accuracyChart = (this.step + "").split(".")[1].length;
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
        if (chart['0'].indexOf('start:') >= 0) {
          this.startValue = parseFloat(chart['0']
            .match(new RegExp(/start:([^;]*)(?=(;|$))/g))['0']
            .replace('start:', ''));
        } else if(this.type == 4) {
          let marks = chart['0'].match(new RegExp(/marks:([^;]*)(?=(;|$))/g))['0']
            .replace('marks:', '').split(',');
          this.startValue = +marks[0];
        }
        if (chart['0'].indexOf('end:') >= 0) {
          this.endValue = this.maxValue = parseFloat(chart['0']
            .match(new RegExp(/end:([^;]*)(?=(;|$))/g))['0']
            .replace('end:', ''));
        } else if(this.type == 4) {
          let marks = chart['0'].match(new RegExp(/marks:([^;]*)(?=(;|$))/g))['0']
            .replace('marks:', '').split(',');
          this.endValue = this.maxValue = +marks[marks.length-1];
        }
        if (chart['0'].indexOf('marks:') >= 0) {
          this.marksLabelsList = chart['0']
            .match(new RegExp(/marks:([^;]*)(?=(;|$))/g))['0']
            .replace('marks:', '').split(',');
          //constants used in anonymous function below
          const precision = Math.max(this.accuracyControl, this.accuracyChart);
          const startValue = this.startValue;
          const endValue = this.endValue;
          let calc = false;
          if(chart['0'].match(new RegExp(/calculate:([^;]*)(?=(;|$))/g))['0']
            .replace('calculate:', '').split(',') == "true") {
            calc = true;
          }
          const calculate = calc;
          this.marksList = this.marksLabelsList.map(function(elem) {
            if((""+elem).includes('/')) {
              let values = (""+elem).split('/').map(Number);
              elem = (values[0]/values[1]);
              if(calculate) elem = elem*(endValue-startValue)+startValue;
            } else if((""+elem).includes('%')) {
              elem = +(""+elem).replace('%', '')/100;
              if(calculate) elem = elem*(endValue-startValue)+startValue;
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
          this.mainColor = "#8ED8DD";
        }
        if (chart['0'].indexOf('selected-color:') >= 0) {
          this.selectedColor = chart['0']
            .match(new RegExp(/selected-color:([^;]*)(?=(;|$))/g))['0']
            .replace('selected-color:', '');
        } else {
          this.selectedColor = "#FFB133";
        }
        if (chart['0'].indexOf('stroke-color:') >= 0) {
          this.strokeColor = chart['0']
            .match(new RegExp(/stroke-color:([^;]*)(?=(;|$))/g))['0']
            .replace('stroke-color:', '');
        } else {
          this.strokeColor = "#FFFFFF";
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
        if(this.control == 1) {
          if(this.valueDisplay == 3) {
            this.minInputValue = 0;
            this.maxInputValue = 1;
            this.stepInput = Math.round(this.step/(this.maxValue-this.startValue)*10000)/10000;
            if(!this.stepInput) this.stepInput = this.step/(this.maxValue-this.startValue);
            this.inputValue = Math.round((this.value-this.startValue)
              /(this.maxValue-this.startValue)*10000)/10000;
          } else if (this.valueDisplay == 4) {
            this.minInputValue = 0;
            this.maxInputValue = 100;
            this.stepInput = Math.round(this.step/(this.maxValue-this.startValue)*10000)/100;
            if(!this.stepInput) this.stepInput = this.step/(this.maxValue-this.startValue)*100;
            this.inputValue = Math.round((this.value-this.startValue)
              /(this.maxValue-this.startValue)*10000)/100;
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
      if(this.value < this.startValue) this.value = this.startValue;
      if(this.value > this.maxValue) this.value = this.maxValue;
      if(this.type == 3) this.value = Math.round(this.value);
      let chartHtml = '';
      let valuePercent = this.value/this.maxValue;
      let chartContainer = document.getElementById('chart-container');
      let chartValueLabelFontSize = 16;
        if(window.innerWidth < 450) {
          chartValueLabelFontSize = 11;
      }
      let size = Math.min(this.chartHeight, window.innerWidth*0.5);
      switch (this.type) {
        default:
        case 1:
          // Chart (type 1 - rectangle)
          chartHtml += '<svg style="height: ' + size + 'px; width:' + size + 'px;">';
          chartHtml += '<rect id="rect2" height="'+size
            +'" width="'+size+'" style="fill: '
            + this.mainColor + '; stroke: ' +
            this.strokeColor + '; stroke-width: '+ this.strokeWidth + '"';
          chartHtml +=  '></rect>';
          chartHtml += '<rect id="rect1" y="'
            +(1 - valuePercent) * size
            +'" height="'+valuePercent*size
            +'" width="'+size+'" style="fill: '
            + this.selectedColor + '; stroke: ' +
          this.strokeColor + '; stroke-width: '+ this.strokeWidth + '"';
          chartHtml +=  '></rect>';

          if(this.valueDisplayChart > 0) {
            let x, y;
            x = size/2;
            if(this.value < this.maxValue/10) {
              y = size - (this.value/this.maxValue*size)-5;
            } else {
              y = size - 0.5*(this.value/this.maxValue*size)+5;
            }
            let valueLabel = '<text text-anchor="middle" x=' + x + ' y=' + y
              + ' fill="' + this.strokeColor
              +'" class="chart-value-label" style="font-size: '
              +chartValueLabelFontSize+'px;">';
            if(this.valueDisplayChart == 1) {
              valueLabel += this.value.toFixed(this.accuracyChart);
            } else if (this.valueDisplayChart == 2) {
              valueLabel += this.value.toFixed(this.accuracyChart) + '/' + this.maxValue;
            } else if (this.valueDisplayChart == 3) {
              valueLabel += (this.value/this.maxValue).toFixed(this.accuracyChart);
            } else if (this.valueDisplayChart == 4) {
              valueLabel += (this.value/this.maxValue*100).toFixed(this.accuracyChart) + '%'
            }
            valueLabel +=  '</text>';
            chartHtml += valueLabel;
          }

          chartHtml += '</svg>';
          chartContainer.innerHTML = chartHtml;
          break;
        case 2:
          // Chart (type 2 - circle)
          let radius = size/2;
          let angle = 2*Math.PI*valuePercent;
          let x = radius + radius*Math.sin(angle);
          let  y = radius - radius*Math.cos(angle);
          chartHtml += '<svg style="height: '
            + size + 'px; width:' + size + 'px;">';
          if(valuePercent <= 0.999) {
            chartHtml += '<circle id="circle2" r="'+radius
            +'" cx="'+radius+'" cy="'+radius+'" style="fill: '
            + this.mainColor + '; stroke: ' +
              this.strokeColor + '; stroke-width: '+ this.strokeWidth + '" />';
            chartHtml += '<path id="circle1" d="M'+ radius +','+ radius
              + ' L' + radius + ',0 A' + radius + ',' + radius;
            if(valuePercent <= 0.5){
              chartHtml += ' 1 0,1';
            } else {
              chartHtml += ' 1 1,1';
            }
            chartHtml += ' ' + x + ', ' + y +' z"';
            chartHtml += 'style="fill: ' + this.selectedColor + '; stroke: ' +
              this.strokeColor + '; stroke-width: '+ this.strokeWidth + '"';
            chartHtml += '></path>';
          } else {
            chartHtml += '<circle id="circle1" r="'+radius
            +'" cx="'+radius+'" cy="'+radius+'"style="fill: '
            + this.selectedColor + '; stroke: ' +
            this.strokeColor + '; stroke-width: '+ this.strokeWidth + '"/>';
          }
          chartHtml += '</svg>';

          if(this.valueDisplayChart > 0) {
            let valueLabel = document.createElement("label");
            valueLabel.classList.add("chart-value-label");
            valueLabel.style.position = "absolute";
            valueLabel.style.color = this.strokeColor;
            valueLabel.style.fontSize = chartValueLabelFontSize + "px";
            if(this.value/this.maxValue < 0.5) {
              valueLabel.style.left = ((chartContainer.clientWidth
                -size)/2+8+x) + "px";
            } else {
              valueLabel.style.right = ((chartContainer.clientWidth
                -size)/2+(+size+8)-x) + "px";
            }
            if(this.value/this.maxValue < 0.25 || this.value/this.maxValue > 0.75) {
              valueLabel.style.top = y-chartValueLabelFontSize + "px";
            } else {
              valueLabel.style.top = y + "px";
            }
            if(this.valueDisplayChart == 1) {
              valueLabel.innerHTML += this.value.toFixed(this.accuracyChart);
            } else if (this.valueDisplayChart == 2) {
              valueLabel.innerHTML += this.value.toFixed(this.accuracyChart)
                 + '/' + this.maxValue;
            } else if (this.valueDisplayChart == 3) {
              valueLabel.innerHTML += (this.value/this.maxValue)
                .toFixed(this.accuracyChart);
            } else if (this.valueDisplayChart == 4) {
              valueLabel.innerHTML += (this.value/this.maxValue*100)
                .toFixed(this.accuracyChart) + '%';
            }
            chartHtml += valueLabel.outerHTML;
          }

          chartContainer.innerHTML = chartHtml;
          break;
        case 3:
          // Chart (type 3 - dots)
          let canvas = document.createElement("canvas");
          requestAnimationFrame(() => {
            chartContainer.innerHTML = chartHtml;
            chartContainer.appendChild(canvas);

            if(this.valueDisplayChart > 0) {
              let valueLabel = document.createElement("label");
              valueLabel.classList.add('chart-value-label');
              valueLabel.style.color = this.strokeColor;
              valueLabel.style.fontSize = chartValueLabelFontSize + "px";
              if(this.valueDisplayChart == 1) {
                valueLabel.innerHTML = ''+ this.value.toFixed(this.accuracyChart);
              } else if (this.valueDisplayChart == 2) {
                valueLabel.innerHTML = ''+ this.value.toFixed(this.accuracyChart) + '/' + this.maxValue;
              } else if (this.valueDisplayChart == 3) {
                valueLabel.innerHTML = ''+ (this.value/this.maxValue).toFixed(this.accuracyChart);
              } else if (this.valueDisplayChart == 4) {
                valueLabel.innerHTML = ''+ (this.value/this.maxValue*100)
                  .toFixed(this.accuracyChart) + '%';
              }
              chartContainer.appendChild(valueLabel);
            }

          });
          canvas.style.height = this.chartHeight+'px';
          canvas.style.width = this.chartHeight*2+'px';
          canvas.style.maxWidth = '100%';
          canvas.style.maxHeight = window.innerWidth*0.5+'px';

          let ctx = canvas.getContext("2d");
          for(let i = 0; i < this.maxValue; i++) {
            if(this.dots[i] == undefined){
              this.dots[i] = {
                x: Math.random() * (canvas.width-this.dotRadius*2),
                y: Math.random() * (canvas.height-this.dotRadius*2),
                radius: this.dotRadius
              };
            }
          }
          this.dotsChartRebuildFunctionId = setInterval(() => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            this.drawDotsChart(this.value, this.maxValue, ctx, canvas);
          }, 80);
          break;
        case 4:
          // Chart (type 4 - slider)
          let precision = Math.max(this.accuracyControl, this.accuracyChart);
          let width  = chartContainer.offsetWidth;
          let indentation = this.pointDiameter + 5;
          chartHtml += '<svg style="width:' + width + 'px; height: 50px;">';
          chartHtml += '<line x1="' + indentation + '" y1="25" x2="'
            + (width-indentation) + '" y2="25" style="stroke:'
            + this.mainColor + '; stroke-width:'
            + this.strokeWidth + '" />';
          width -= indentation*2;
          for(let i = 0; i < (this.endValue-this.startValue); i+= this.step) {
            let position = (i*width/(this.endValue-this.startValue))+indentation;
            let point = Number((i+this.startValue).toFixed(precision));
            if(this.marksList.includes(point)) {
              let label = this.marksLabelsList[this.marksList.indexOf(point)];
              chartHtml += '<circle cx="' + position + '" cy="25" r="'
                + (this.markDiameter/2) + '" fill="' + this.strokeColor + '" />';
              let textPosition = ((point-this.startValue)/(this.endValue
                -this.startValue)*width + indentation);
              if(i == 0) {
                chartHtml += '<text x="' + (this.markDiameter/2)
                + '" y="50" fill="' + this.strokeColor
                +'" style="font-size: '+chartValueLabelFontSize+'px;" text-anchor="start">'
                + label + '</text>';
              } else {
                chartHtml += '<text x="' + textPosition
                + '" y="50" fill="'+this.strokeColor
                +'" style="font-size: '+chartValueLabelFontSize+'px;" text-anchor="middle">'
                + label + '</text>';
              }
            } else {
              chartHtml += '<circle cx="' + position + '" cy="25" r="'
                + (this.pointDiameter/2) + '" fill="' + this.strokeColor + '" />';
            }
          }
          chartHtml += '<circle cx="' + (width+indentation) + '" cy="25" r="'
            + (this.markDiameter/2) + '" fill="' + this.strokeColor + '" />';
          if(this.marksList[this.marksList.length-1].toFixed(precision)
            == this.endValue.toFixed(precision)) {
            let label = this.marksLabelsList[this.marksList.length-1];
            chartHtml += '<text x="' + (width+indentation*2-(this.markDiameter/2))
              + '" y="50" fill="' + this.strokeColor
              +'" style="font-size: '+chartValueLabelFontSize+'px;" text-anchor="end">'
              + label + '</text>';
          }
          let currentPointX = (this.value-this.startValue)/(this.endValue
            -this.startValue)*width + indentation;
          chartHtml += '<circle cx="' + currentPointX + '" cy="25" r="'
            + this.markDiameter + '" fill="' + this.selectedColor + '" />';

          if(this.valueDisplayChart > 0) {
            let currentPointLabel = '<text ';
            let currentValueFixed = Number((this.value).toFixed(precision));
            let startValueFixed = Number((this.startValue).toFixed(precision));
            let endValueFixed = Number((this.endValue).toFixed(precision));
            if(currentValueFixed == startValueFixed) {
              currentPointLabel += 'x=' + (this.markDiameter/2) + ' text-anchor="start"';
            } else if (currentValueFixed == endValueFixed) {
              currentPointLabel += 'x=' + (width+indentation*2-(this.markDiameter/2))
               + ' text-anchor="end"';
            } else {
              currentPointLabel += 'x=' + currentPointX + ' text-anchor="middle"';
            }
            currentPointLabel += '" y="15" fill="' + this.strokeColor
              +'"  class="chart-value-label" style="font-size: '+chartValueLabelFontSize+'px;">';
            if(this.valueDisplayChart == 1) {
              currentPointLabel += this.value.toFixed(this.accuracyChart);
            } else if (this.valueDisplayChart == 2) {
              currentPointLabel += this.value.toFixed(this.accuracyChart) + '/' + this.maxValue;
            } else if (this.valueDisplayChart == 3) {
              currentPointLabel += ((this.value-this.startValue)
                /(this.maxValue-this.startValue)).toFixed(this.accuracyChart);
            } else if (this.valueDisplayChart == 4) {
              currentPointLabel += ((this.value-this.startValue)
                /(this.maxValue-this.startValue)*100).toFixed(this.accuracyChart) + '%';
            }
            currentPointLabel +=  '</text>';
            chartHtml += currentPointLabel;
          }
          chartHtml += '</svg>';
          chartContainer.innerHTML = chartHtml;
          break;
      }
      this.ref.detectChanges();
    }

    // function to set value by clicking on top slider
    private setClickPosition(event) {
      let chartContainer = document.getElementById('chart-container');
      let pos = getAbsolutePosition(chartContainer);

      let accuracy = this.accuracyControl;
      if(this.valueDisplay == 3 || this.valueDisplay == 4) {
        accuracy = Math.max(accuracy, 5);
      }

      if (this.type == 1) {
        let y = event.pageY - pos.y;
        let height  = chartContainer.offsetHeight;
        this.value = this.maxValue - y*this.maxValue / height;

        // find the closest point
        let point = 0;
        let diff = Math.abs(this.value - point);
        for (let i = 0; i <= this.maxValue; i+= this.step) {
          let newdiff = Math.abs(this.value - i);
          if (newdiff < diff) {
            diff = newdiff;
            point = i;
          }
        }
        Math.abs(this.value - this.maxValue) < diff ?
          this.value = this.maxValue : this.value = Math.round(point*Math.pow(10,
            accuracy))/Math.pow(10, accuracy);
      } else if (this.type == 2) {
        let x = event.pageX - pos.x;
        let y = event.pageY - pos.y;
        let height  = chartContainer.offsetHeight;
        let width  = chartContainer.offsetWidth;
        let x0 = width/2;
        let y0 = height/2;
        let xc = x - x0;
        let yc = y0 - y;
        let angle = Math.atan(xc/yc);
        this.value = angle/(2*Math.PI)*this.maxValue; // I
        if( (xc>0 && yc<0) || (xc<=0 && yc<0) ) { // II-III
          this.value += 0.5*this.maxValue;
        } else if(xc<0 && yc>=0) { // IV
          this.value += this.maxValue;
        }

        // find the closest point
        let point = 0;
        let diff = Math.abs(this.value - point);
        for (let i = 0; i <= this.maxValue; i+= this.step) {
          let newdiff = Math.abs(this.value - i);
          if (newdiff < diff) {
            diff = newdiff;
            point = i;
          }
        }
        Math.abs(this.value - this.maxValue) < diff ?
          this.value = this.maxValue : this.value = Math.round(point*Math.pow(10,
            accuracy))/Math.pow(10, accuracy);
      } else if (this.type == 4) {
        let x = event.pageX - pos.x;
        let circleDiameter = 2*this.dotRadius;
        let indentation = circleDiameter + 5;
        let width  = chartContainer.offsetWidth - indentation*2;
        this.value = (x - indentation)*(this.endValue
          -this.startValue) / width + this.startValue;

        // find the closest point
        let point = this.startValue;
        let diff = Math.abs(this.value - point);
        for (let i = this.startValue; i <= this.endValue; i+= this.step) {
          let newdiff = Math.abs(this.value - i);
          if (newdiff < diff) {
            diff = newdiff;
            point = i;
          }
        }
        Math.abs(this.value - this.endValue) < diff ?
          this.value = this.endValue : this.value = Math.round(point*Math.pow(10,
            accuracy))/Math.pow(10, accuracy);

        if(this.value < this.startValue)
          this.value = this.startValue;
        else if (this.value > this.endValue)
          this.value = this.endValue;
      }
      if(this.type == 1 || this.type == 2 || this.type == 4) {
        let promise = new Promise((resolve) => {
          if(this.control == 1 && this.valueDisplay == 3) {
            this.inputValue = Math.round((this.value-this.startValue)
              /(this.maxValue-this.startValue)*Math.pow(10, accuracy))/Math.pow(10, accuracy);
          } else if(this.control == 1 && this.valueDisplay == 4){
            this.inputValue = Math.round((this.value-this.startValue)
              /(this.maxValue-this.startValue)*Math.pow(10, accuracy+2))/Math.pow(10, accuracy);
          } else {
            this.inputValue = Math.round(this.value*Math.pow(10,
              accuracy))/Math.pow(10, accuracy);
          }
          resolve();
        }).then(() => {
          this.buildChart();
        });
      }
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
      canvas:HTMLCanvasElement, dot) {
      ctx.strokeStyle = this.strokeColor;
      ctx.lineWidth   = this.strokeWidth;
      if(type == 1) {
        ctx.fillStyle = this.mainColor;
      }
      if(type == 2) {
        ctx.fillStyle = this.selectedColor;
      }
      dot.x += Math.random() * 2 - 1;
      dot.y += Math.random() * 2 - 1;

      // Check if dot goes beyond the field
      let dotDiameter = dot.radius*2;
      if(dot.x > canvas.width-dotDiameter)
        dot.x = canvas.width-dotDiameter;
      if(dot.x < dotDiameter)
        dot.x = dotDiameter;
      if(dot.y > canvas.height-dotDiameter)
        dot.y = canvas.height-dotDiameter;
      if(dot.y < dotDiameter)
        dot.y = dotDiameter;

      ctx.beginPath();
      ctx.arc(dot.x, dot.y, dot.radius, 0, Math.PI*2, true);
      ctx.fill();
      ctx.stroke();
      return dot;
    }

    // remove dot chart rebuild function if it exists
    private destroyDotsChart() {
      if (this.dotsChartRebuildFunctionId)
        clearInterval(this.dotsChartRebuildFunctionId);
    }

}

// function to get absolute position of HTML element
function getAbsolutePosition(element) {
  let r = { x: element.offsetLeft, y: element.offsetTop };
  if (element.offsetParent) {
    let tmp = getAbsolutePosition(element.offsetParent);
    r.x += tmp.x;
    r.y += tmp.y;
  }
  return r;
};
