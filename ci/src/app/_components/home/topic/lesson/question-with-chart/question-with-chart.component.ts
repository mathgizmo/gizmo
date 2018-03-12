import { Component, Inject, OnDestroy, ChangeDetectionStrategy, 
    Input, OnChanges, SimpleChanges } from '@angular/core';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';
import {MatSliderModule} from '@angular/material/slider';

@Component({
    selector: 'question-with-chart',
    templateUrl: 'question-with-chart.component.html' ,
    styleUrls: ['question-with-chart.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class QuestionWithChartComponent implements OnDestroy, OnChanges {
    
    @Input() question: string;
    @Input() chartHeight: number; // dimension of chart area in px

    private mainColor: string = "#f7f7f7";
    private selectedColor: string = "#ff4444";
    private strokeColor: string = "#111";
    private strokeWidth: number = 1;

    private dotRadius: number = 4;

    private chartType: number = 1;
    private chartControl: number = 0;
    private chartValue: number = 0.50;
    private chartMaxValue: number = 0;
    private chartStartValue: number = 0;
    private chartEndValue: number = 3;
    private chartStep: number = 0.5;

    private initialized = false;
    private oldQuestion: string;

    private chart4Value: number;

    private valueWhenMaxExists : number;
    private percentValue: number;
    private displayFraction: boolean = false;

    chart: SafeHtml;
    private dotsChartRebuildFunctionId; // id of function which rebuild dots chart
    private dots;
   
    constructor(private sanitizer: DomSanitizer){
      this.dots = [];
      if(!this.chartHeight)
        this.chartHeight=250; 
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
        this.buildChart();
        this.valueWhenMaxExists =  Math.round(
          this.chartValue*this.chartMaxValue);
        this.percentValue = Math.round(this.chartValue*100);
        this.chart4Value = (this.chartValue-this.chartStartValue)
          /(this.chartEndValue-this.chartStartValue);

        // setup equation in LaTeX
        setTimeout(function() {
          MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
        }, 50);
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
        if (chart['0'].indexOf('start-value:') >= 0) {
          this.chartStartValue = parseFloat(chart['0']
            .match(new RegExp(/start-value:([^;]*)(?=(;|$))/g))['0']
            .replace('start-value:', ''));
        }
        if (chart['0'].indexOf('end-value:') >= 0) {
          this.chartEndValue = parseFloat(chart['0']
            .match(new RegExp(/end-value:([^;]*)(?=(;|$))/g))['0']
            .replace('end-value:', ''));
        }
        if (chart['0'].indexOf('step:') >= 0) {
          this.chartStep = parseFloat(chart['0']
            .match(new RegExp(/step:([^;]*)(?=(;|$))/g))['0']
            .replace('step:', ''));
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
          this.strokeWidth = chart['0']
            .match(new RegExp(/stroke-width:([^;]*)(?=(;|$))/g))['0']
            .replace('stroke-width:', '');
        }
        if (chart['0'].indexOf('control:') >= 0)
          this.chartControl = parseFloat(chart['0']
            .match(new RegExp(/control:([^;]*)(?=(;|$))/g))['0']
            .replace('control:', ''));
        if(this.chartType == 4) 
          this.chartValue = this.chartStartValue;
        this.initialized = true;
      }
      let chartHtml = this.question
        .replace(new RegExp(/%%chart(.*)(?=%)%/g), "");
      switch (this.chartType) {
        default:
        case 1:
          // Chart (type 1 - rectangle)
          this.displayFraction = false;
          chartHtml += '<svg style="height: '
            + this.chartHeight + '; width:' + this.chartHeight + ';">';
          chartHtml += '<rect id="rect2" style="height:'
            + this.chartHeight +' !important; width: 100%;';
          chartHtml += ' fill: ' + this.mainColor + '; stroke: ' + 
            this.strokeColor + '; stroke-width: '+ this.strokeWidth + '"';
          chartHtml +=  '></rect>';
          chartHtml += '<rect id="rect1" style="y: ' +
            (1 - this.chartValue) * this.chartHeight + '; height:' +
            this.chartValue*this.chartHeight +' !important; width: 100%;';
          chartHtml += ' fill: ' + this.selectedColor + '; stroke: ' + 
          this.strokeColor + '; stroke-width: '+ this.strokeWidth + '"';
          chartHtml +=  '></rect>';
          chartHtml += '</svg>';
          this.chart = this.sanitizer.bypassSecurityTrustHtml(chartHtml);
          break;
        case 2:
          // Chart (type 2 - circle)
          this.displayFraction = false;
          let radius = this.chartHeight/2;
          let angle = 2*Math.PI*this.chartValue;
          let x = radius + radius*Math.sin(angle);
          let  y = radius - radius*Math.cos(angle);
          chartHtml += '<svg style="height: '
            + this.chartHeight + '; width:' + this.chartHeight + ';">';
          if(this.chartValue <= 0.999) {
            chartHtml += '<circle id="circle2" style="r: ' + radius 
              + ' !important; cx: '+ radius + ' !important; cy: ' 
              + radius + ' !important;';
            chartHtml += ' fill: ' + this.mainColor + '; stroke: ' + 
              this.strokeColor + '; stroke-width: '+ this.strokeWidth + '" />';
            chartHtml += '<path id="circle1" d="M'+ radius +','+ radius 
              + ' L' + radius + ',0 A' + radius + ',' + radius;
            if(this.chartValue <= 0.5){
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
          this.chart = this.sanitizer.bypassSecurityTrustHtml(chartHtml);
          break;
        case 3:
          // Chart (type 3 - dots)
          this.displayFraction = true;
          let chartContainer = document.getElementById('chart-container');
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
          this.displayFraction = false;
          let circleDiameter = 2*this.dotRadius;
          let width  = document.getElementById('chart-container').offsetWidth;
          let indentation = circleDiameter + 5;
          chartHtml += '<svg style="width:' + width + 'px; height: 50px;">';
          chartHtml += '<line x1="' + indentation + '" y1="10" x2="' 
            + (width-indentation) + '" y2="10" style="stroke:' 
            + this.mainColor + '; stroke-width:'
            + this.strokeWidth + '" />';
          width -= indentation*2;
          chartHtml += '<line x1="' + indentation + '" y1="10" x2="' 
            + ((this.chartValue-this.chartStartValue)/(this.chartEndValue
            -this.chartStartValue)*width + indentation) 
            + '" y2="10" style="stroke:' 
            + this.selectedColor + '; stroke-width:'
            + this.strokeWidth + '" />';
          for(let i = 0; i < (this.chartEndValue-this.chartStartValue); 
            i+= this.chartStep) {
            let position = (i*width
              /(this.chartEndValue-this.chartStartValue))+indentation;
            chartHtml += '<text x="' + position
              + '" y="35" fill="' + this.strokeColor 
              +'" font-size="16" text-anchor="middle">' 
              + (this.chartStartValue+i).toFixed(1) + '</text>';
            chartHtml += '<circle cx="' + position + '" cy="10" r="' 
              + (circleDiameter/2) + '" fill="' + this.strokeColor + '" />';
          }
          chartHtml += '<text x="' + (width+indentation)
            + '" y="35" fill="' + this.strokeColor 
            +'" font-size="16" text-anchor="middle">' 
            + this.chartEndValue.toFixed(1) + '</text>';
          chartHtml += '<circle cx="' + (width+indentation) + '" cy="10" r="' 
            + (circleDiameter/2) + '" fill="' + this.strokeColor + '" />';
          chartHtml += '</svg>';
          this.chart = this.sanitizer.bypassSecurityTrustHtml(chartHtml);
          break;
      }
    }

    // function to draw Dots Chart
    private drawDotsChart(dotsPercent: number, 
      maxDotsNum: number, ctx: CanvasRenderingContext2D,
      canvas: HTMLCanvasElement) {
      let dotsNum = Math.round(maxDotsNum*dotsPercent);
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