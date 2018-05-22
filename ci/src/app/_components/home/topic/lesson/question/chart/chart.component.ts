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

    private mainColor: string = "#f7f7f7";
    private selectedColor: string = "#ff4444";
    private strokeColor: string = "#111";
    private strokeWidth: number = 1;
    private markDiameter: number = 3;
    private pointDiameter: number = 1;

    private dotRadius: number = 4;

    private type: number = 1;
    private control: number = 0;
    private valueDisplay: number = 0;
    private value: number = 0.50;
    private maxValue: number = 0;
    private startValue = 0;
    private endValue = 1;
    private step: number = 0.5;
    private marksList: number[]= [0, 0.5, 1];

    private initialized = false;
    private oldQuestion: string;

    private percentValue: number;
    private setClickPositionEventId: boolean = false;
    private precision: number = 2; // number of decimals (0 - integer)

    private dotsChartRebuildFunctionId; // id of function which rebuild dots chart
    private dots;
   
    constructor(private ref:ChangeDetectorRef){
      this.dots = [];
      if(!this.chartHeight)
        this.chartHeight=250;
    }

    ngOnInit() {
    }

    ngOnDestroy() {
      this.destroyDotsChart();
      this.removeSetClickPositionEvent();
    }

    ngOnChanges(changes: SimpleChanges) {
        if (this.oldQuestion != this.question) {
            this.oldQuestion = this.question;
            this.initialized = false;
        }
        this.destroyDotsChart();
        this.removeSetClickPositionEvent();
        this.buildChart();
        this.percentValue = Math.round(this.value/this.maxValue*100);
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
        if (chart['0'].indexOf('value-display:') >= 0) {
          this.valueDisplay = +chart['0']
            .match(new RegExp(/value-display:([^;]*)(?=(;|$))/g))['0']
            .replace('value-display:', '');
        } else {
          this.valueDisplay = 0;
        }
        if(this.valueDisplay > 4) this.valueDisplay = 0;
        if (chart['0'].indexOf('value:') >= 0) {
          this.value = parseFloat(chart['0']
            .match(new RegExp(/value:([^;]*)(?=(;|$))/g))['0']
            .replace('value:', ''));
        }
        if (chart['0'].indexOf('max:') >= 0) {
          this.maxValue = parseFloat(chart['0']
            .match(new RegExp(/max:([^;]*)(?=(;|$))/g))['0']
            .replace('max:', ''));
        }
        if (chart['0'].indexOf('step:') >= 0) {
          this.step = parseFloat(chart['0']
            .match(new RegExp(/step:([^;]*)(?=(;|$))/g))['0']
            .replace('step:', ''));
          Number.isInteger(this.step) ? this.precision = 0
            : this.precision = (this.step + "").split(".")[1].length;
        }
        if (chart['0'].indexOf('marks:') >= 0) {
          this.marksList = chart['0']
            .match(new RegExp(/marks:([^;]*)(?=(;|$))/g))['0']
            .replace('marks:', '').split(',').map(Number);
          const precision = this.precision; //used in anonymous function below
          this.marksList = this.marksList.map(function(elem){
            return Number(elem.toFixed(precision));
          });
          this.maxValue = this.marksList[this.marksList.length-1];
        }
        if (chart['0'].indexOf('main-color:') >= 0) {
          this.mainColor = chart['0']
            .match(new RegExp(/main-color:([^;]*)(?=(;|$))/g))['0']
            .replace('main-color:', '');
        }
        if (chart['0'].indexOf('selected-color:') >= 0) {
          this.selectedColor = chart['0']
            .match(new RegExp(/selected-color:([^;]*)(?=(;|$))/g))['0']
            .replace('selected-color:', '');
        }
        if (chart['0'].indexOf('stroke-color:') >= 0) {
          this.strokeColor = chart['0']
            .match(new RegExp(/stroke-color:([^;]*)(?=(;|$))/g))['0']
            .replace('stroke-color:', '');
        }
        if (chart['0'].indexOf('stroke-width:') >= 0) {
          this.strokeWidth = +chart['0']
            .match(new RegExp(/stroke-width:([^;]*)(?=(;|$))/g))['0']
            .replace('stroke-width:', '');
        }
        if (chart['0'].indexOf('mark-diameter:') >= 0) {
          this.markDiameter = +chart['0']
            .match(new RegExp(/mark-diameter:([^;]*)(?=(;|$))/g))['0']
            .replace('mark-diameter:', '');
        }
        if (chart['0'].indexOf('point-diameter:') >= 0) {
          this.pointDiameter = +chart['0']
            .match(new RegExp(/point-diameter:([^;]*)(?=(;|$))/g))['0']
            .replace('point-diameter:', '');
        }
        if (chart['0'].indexOf('control:') >= 0)
          this.control = +chart['0']
            .match(new RegExp(/control:([^;]*)(?=(;|$))/g))['0']
            .replace('control:', '');
        this.initialized = true;
      }
      let chartHtml = '';
      if(this.type == 3) {
        this.step >= 1 
          ? this.step = Math.round(this.step)
          : this.step = 1;
      }
      let valuePercent = this.value/this.maxValue;
      let chartContainer = document.getElementById('chart-container');
      switch (this.type) {
        default:
        case 1:
          // Chart (type 1 - rectangle)
          chartHtml += '<svg style="height: '
            + this.chartHeight + '; width:' + this.chartHeight + ';">';
          chartHtml += '<rect id="rect2" style="height:'
            + this.chartHeight +' !important; width: 100%;';
          chartHtml += ' fill: ' + this.mainColor + '; stroke: ' + 
            this.strokeColor + '; stroke-width: '+ this.strokeWidth + '"';
          chartHtml +=  '></rect>';
          chartHtml += '<rect id="rect1" style="y: ' +
            (1 - valuePercent) * this.chartHeight + '; height:' +
            valuePercent*this.chartHeight +' !important; width: 100%;';
          chartHtml += ' fill: ' + this.selectedColor + '; stroke: ' + 
          this.strokeColor + '; stroke-width: '+ this.strokeWidth + '"';
          chartHtml +=  '></rect>';
          chartHtml += '</svg>';
          chartContainer.innerHTML = chartHtml;
          if(!this.setClickPositionEventId) {
            chartContainer.addEventListener('click', this.setClickPosition.bind(this));
            this.setClickPositionEventId = true;
          }
          break;
        case 2:
          // Chart (type 2 - circle)
          let radius = this.chartHeight/2;
          let angle = 2*Math.PI*valuePercent;
          let x = radius + radius*Math.sin(angle);
          let  y = radius - radius*Math.cos(angle);
          chartHtml += '<svg style="height: '
            + this.chartHeight + '; width:' + this.chartHeight + ';">';
          if(valuePercent <= 0.999) {
            chartHtml += '<circle id="circle2" style="r: ' + radius 
              + ' !important; cx: '+ radius + ' !important; cy: ' 
              + radius + ' !important;';
            chartHtml += ' fill: ' + this.mainColor + '; stroke: ' + 
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
            chartHtml += '<circle id="circle1" style="r: ' + radius 
              + ' !important; cx: '+ radius + ' !important; cy: ' 
              + radius + ' !important;'
              chartHtml += 'fill: ' + this.selectedColor + '; stroke: ' + 
              this.strokeColor + '; stroke-width: '+ this.strokeWidth + '"/>';
          }
          chartHtml += '</svg>';
          chartContainer.innerHTML = chartHtml;
          if(!this.setClickPositionEventId) {
            chartContainer.addEventListener('click', this.setClickPosition.bind(this));
            this.setClickPositionEventId = true;
          }
          break;
        case 3:
          // Chart (type 3 - dots)
          let canvas = document.createElement("canvas");
          requestAnimationFrame(() => {
            chartContainer.innerHTML = chartHtml;
            chartContainer.appendChild(canvas);
          });
          canvas.style.height = this.chartHeight+'px';
          canvas.style.width = chartContainer.style.width;
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
          let width  = chartContainer.offsetWidth;
          let indentation = this.pointDiameter + 5;
          this.startValue = Math.min.apply(null, this.marksList);
          this.endValue = this.maxValue = 
            Math.max.apply(null, this.marksList);
          chartHtml += '<svg style="width:' + width + 'px; height: 50px;">';
          chartHtml += '<line x1="' + indentation + '" y1="10" x2="' 
            + (width-indentation) + '" y2="10" style="stroke:' 
            + this.mainColor + '; stroke-width:'
            + this.strokeWidth + '" />';
          width -= indentation*2;

          for(let i = 0; i < (this.endValue-this.startValue); i+= this.step) {
            let position = (i*width/(this.endValue-this.startValue))+indentation;
            let point = Number((i+this.startValue).toFixed(this.precision));
            if(this.marksList.includes(point)) {
              chartHtml += '<circle cx="' + position + '" cy="10" r="' 
                + (this.markDiameter/2) + '" fill="' + this.strokeColor + '" />';
              let textPosition = ((point-this.startValue)/(this.endValue
                -this.startValue)*width + indentation);
              if(i == 0) {
                chartHtml += '<text x="' + (this.markDiameter/2)
                + '" y="35" fill="' + this.strokeColor 
                +'" font-size="16" text-anchor="start">' 
                + point + '</text>';
              } else {
                chartHtml += '<text x="' + textPosition
                + '" y="35" fill="' + this.strokeColor 
                +'" font-size="16" text-anchor="middle">' 
                + point + '</text>';
              }
            } else {
              chartHtml += '<circle cx="' + position + '" cy="10" r="' 
                + (this.pointDiameter/2) + '" fill="' + this.strokeColor + '" />';
            } 
          }
          chartHtml += '<circle cx="' + (width+indentation) + '" cy="10" r="' 
            + (this.markDiameter/2) + '" fill="' + this.strokeColor + '" />';
          chartHtml += '<text x="' + (width+indentation*2-(this.markDiameter/2))
            + '" y="35" fill="' + this.strokeColor 
            +'" font-size="16" text-anchor="end">' 
            + this.marksList[this.marksList.length-1] + '</text>';
          /* Old version (can be deleted)
          for(let i = 0; i < this.marksList.length; i++) {
            let position = ((this.marksList[i]-this.startValue)/(this.endValue
              -this.startValue)*width + indentation);
            chartHtml += '<text x="' + position
              + '" y="35" fill="' + this.strokeColor 
              +'" font-size="16" text-anchor="middle">' 
              + this.marksList[i] + '</text>';
          }*/
          let currentPointX = (this.value-this.startValue)/(this.endValue
            -this.startValue)*width + indentation;
          chartHtml += '<circle cx="' + currentPointX + '" cy="10" r="' 
            + this.markDiameter + '" fill="' + this.selectedColor + '" />';
          chartHtml += '</svg>';
          chartContainer.innerHTML = chartHtml;

          if(!this.setClickPositionEventId) {
            chartContainer.addEventListener('click', this.setClickPosition.bind(this));
            this.setClickPositionEventId = true;
          }

          break;
      }
      this.ref.detectChanges();
    }

    // function to set value by clicking on top slider
    private setClickPosition(event){
      let chartContainer = document.getElementById('chart-container');
      let pos = getAbsolutePosition(chartContainer);
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
            this.precision))/Math.pow(10, this.precision);;

        this.buildChart();
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
            this.precision))/Math.pow(10, this.precision);;

        this.buildChart();
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
          this.value = this.endValue : this.value = point;

        if(this.value < this.startValue) 
          this.value = this.startValue;
        else if (this.value > this.endValue) 
          this.value = this.endValue;

        this.buildChart();
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

    // remove Set Click Position Event if it exists
    private removeSetClickPositionEvent() {
      if(this.setClickPositionEventId) {
        let chartContainer = document.getElementById('chart-container');
        chartContainer.removeEventListener('click', this.setClickPosition.bind(this));
        this.setClickPositionEventId = false;
      }
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