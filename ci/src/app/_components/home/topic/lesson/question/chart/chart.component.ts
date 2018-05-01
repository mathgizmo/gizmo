import { Component, Inject, OnInit, OnDestroy, ChangeDetectionStrategy, 
    Input, OnChanges, SimpleChanges } from '@angular/core';
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

    private chartType: number = 1;
    private chartControl: number = 0;
    private chartValue: number = 0.50;
    private chartMaxValue: number = 0;
    private startValue = 0;
    private endValue = 1;
    private chartStep: number = 0.5;
    private chartMarksList: number[]= [0, 0.5, 1];

    private initialized = false;
    private oldQuestion: string;

    private percentValue: number;
    private setClickPositionEventId: boolean = false;
    private precision: number = 2; // number of decimals (0 - integer)

    private dotsChartRebuildFunctionId; // id of function which rebuild dots chart
    private dots;
   
    constructor(){
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
        this.percentValue = Math.round(this.chartValue/this.chartMaxValue*100);
    }

    // function to build charts
    private buildChart() {
      if ( !this.initialized ) {
        let chart = this.question
          .match(new RegExp(/[^{}]+(?=\}%%)/g));
        if (chart['0'].indexOf('type:') >= 0)
          this.chartType = parseFloat(chart['0']
            .match(new RegExp(/type:([^;]*)(?=(;|$))/g))['0']
            .replace('type:', ''));
        if (chart['0'].indexOf('value:') >= 0)
          this.chartValue = parseFloat(chart['0']
            .match(new RegExp(/value:([^;]*)(?=(;|$))/g))['0']
            .replace('value:', ''));
        if (chart['0'].indexOf('max:') >= 0) {
          this.chartMaxValue = parseFloat(chart['0']
            .match(new RegExp(/max:([^;]*)(?=(;|$))/g))['0']
            .replace('max:', ''));
        }
        if (chart['0'].indexOf('step:') >= 0) {
          this.chartStep = parseFloat(chart['0']
            .match(new RegExp(/step:([^;]*)(?=(;|$))/g))['0']
            .replace('step:', ''));
          Number.isInteger(this.chartStep) ? this.precision = 0
            : this.precision = (this.chartStep + "").split(".")[1].length;
        }
        if (chart['0'].indexOf('marks:') >= 0) {
          this.chartMarksList = chart['0']
            .match(new RegExp(/marks:([^;]*)(?=(;|$))/g))['0']
            .replace('marks:', '').split(',').map(Number);
          const precision = this.precision; //used in anonymous function below
          this.chartMarksList = this.chartMarksList.map(function(elem){
            return Number(elem.toFixed(precision));
          });
          this.chartMaxValue = this.chartMarksList[this.chartMarksList.length-1];
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
          this.chartControl = parseFloat(chart['0']
            .match(new RegExp(/control:([^;]*)(?=(;|$))/g))['0']
            .replace('control:', ''));
        this.initialized = true;
      }
      let chartHtml = '';
      if(this.chartType == 3) {
        this.chartStep >= 1 
          ? this.chartStep = Math.round(this.chartStep)
          : this.chartStep = 1;
      }
      let chartValuePercent = this.chartValue/this.chartMaxValue;
      let chartContainer = document.getElementById('chart-container');
      switch (this.chartType) {
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
            (1 - chartValuePercent) * this.chartHeight + '; height:' +
            chartValuePercent*this.chartHeight +' !important; width: 100%;';
          chartHtml += ' fill: ' + this.selectedColor + '; stroke: ' + 
          this.strokeColor + '; stroke-width: '+ this.strokeWidth + '"';
          chartHtml +=  '></rect>';
          chartHtml += '</svg>';
          chartContainer.innerHTML = chartHtml;
          break;
        case 2:
          // Chart (type 2 - circle)
          let radius = this.chartHeight/2;
          let angle = 2*Math.PI*chartValuePercent;
          let x = radius + radius*Math.sin(angle);
          let  y = radius - radius*Math.cos(angle);
          chartHtml += '<svg style="height: '
            + this.chartHeight + '; width:' + this.chartHeight + ';">';
          if(chartValuePercent <= 0.999) {
            chartHtml += '<circle id="circle2" style="r: ' + radius 
              + ' !important; cx: '+ radius + ' !important; cy: ' 
              + radius + ' !important;';
            chartHtml += ' fill: ' + this.mainColor + '; stroke: ' + 
              this.strokeColor + '; stroke-width: '+ this.strokeWidth + '" />';
            chartHtml += '<path id="circle1" d="M'+ radius +','+ radius 
              + ' L' + radius + ',0 A' + radius + ',' + radius;
            if(chartValuePercent <= 0.5){
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
          for(let i = 0; i < this.chartMaxValue; i++) {
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
            this.drawDotsChart(this.chartValue, this.chartMaxValue, ctx, canvas);
          }, 80);
          break;
        case 4:
          // Chart (type 4 - slider)
          let width  = chartContainer.offsetWidth;
          let indentation = this.pointDiameter + 5;
          this.startValue = Math.min.apply(null, this.chartMarksList);
          this.endValue = Math.max.apply(null, this.chartMarksList);
          chartHtml += '<svg style="width:' + width + 'px; height: 50px;">';
          chartHtml += '<line x1="' + indentation + '" y1="10" x2="' 
            + (width-indentation) + '" y2="10" style="stroke:' 
            + this.mainColor + '; stroke-width:'
            + this.strokeWidth + '" />';
          width -= indentation*2;

          for(let i = 0; i < (this.endValue-this.startValue); i+= this.chartStep) {
            let position = (i*width/(this.endValue-this.startValue))+indentation;
            let point = Number((i+this.startValue).toFixed(this.precision));
            if(this.chartMarksList.includes(point)) {
              chartHtml += '<circle cx="' + position + '" cy="10" r="' 
                + (this.markDiameter/2) + '" fill="' + this.strokeColor + '" />';
              let textPosition = ((point-this.startValue)/(this.endValue
                -this.startValue)*width + indentation);
              chartHtml += '<text x="' + textPosition
                + '" y="35" fill="' + this.strokeColor 
                +'" font-size="16" text-anchor="middle">' 
                + point + '</text>';
            } else {
              chartHtml += '<circle cx="' + position + '" cy="10" r="' 
                + (this.pointDiameter/2) + '" fill="' + this.strokeColor + '" />';
            } 
          }
          chartHtml += '<circle cx="' + (width+indentation) + '" cy="10" r="' 
            + (this.markDiameter/2) + '" fill="' + this.strokeColor + '" />';
          /* Old version (can be deleted)
          for(let i = 0; i < this.chartMarksList.length; i++) {
            let position = ((this.chartMarksList[i]-this.startValue)/(this.endValue
              -this.startValue)*width + indentation);
            chartHtml += '<text x="' + position
              + '" y="35" fill="' + this.strokeColor 
              +'" font-size="16" text-anchor="middle">' 
              + this.chartMarksList[i] + '</text>';
          }*/
          let currentPointX = (this.chartValue-this.startValue)/(this.endValue
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
    }

    // function to set value by clicking on top slider
    private setClickPosition(event){
      let chartContainer = document.getElementById('chart-container');
      var pos = getAbsolutePosition(chartContainer);
      let x = event.pageX - pos.x;
      let circleDiameter = 2*this.dotRadius;
      let indentation = circleDiameter + 5;
      let width  = chartContainer.offsetWidth - indentation*2;
      this.chartValue = (x - indentation)*(this.endValue
        -this.startValue) / width + this.startValue;

      // find the closest point
      let point = this.startValue;
      let diff = Math.abs(this.chartValue - point);
      for (let i = this.startValue; i <= this.endValue; i+= this.chartStep) {
        let newdiff = Math.abs(this.chartValue - i);
        if (newdiff < diff) {
           diff = newdiff;
           point = i;
        }
      }
      Math.abs(this.chartValue - this.endValue) < diff ?
        this.chartValue = this.endValue : this.chartValue = point;

      if(this.chartValue < this.startValue) 
        this.chartValue = this.startValue;
      else if (this.chartValue > this.endValue) 
        this.chartValue = this.endValue;

      this.buildChart();
      if(this.chartControl > 0) {
        document.getElementById('inputValue').focus();
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
        document.getElementById('chart-container')
          .removeEventListener('click', this.setClickPosition);
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