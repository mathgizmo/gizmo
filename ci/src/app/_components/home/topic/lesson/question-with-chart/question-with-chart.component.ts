import { Component, Inject, OnInit, OnDestroy, 
    ChangeDetectionStrategy, Input, OnChanges, SimpleChanges } from '@angular/core';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';
import {MatSliderModule} from '@angular/material/slider';

@Component({
    selector: 'question-with-chart',
    templateUrl: 'question-with-chart.component.html' ,
    styleUrls: ['question-with-chart.component.css'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class QuestionWithChartComponent implements OnInit, OnDestroy, OnChanges {
    
    @Input() question: string;
    @Input() chartHeight: number; // dimension of chart area in px

    private mainColor: string = "#f7f7f7";
    private selectedColor: string = "#ff4444";
    private strokeColor: string = "#111";
    
    private strokeWidth: number = 1;
    private bubleRadius: number = 4;

    chart: SafeHtml;
    private bubleChartRebuildFunctionId; // id of function which rebuild buble chart

    private chartType: number = 1;
    private chartValue: number = 0.50;
    private chartMaxValue: number = 100;
    private chartControl: number = 0;
    private initialized = false;
    private oldQuestion: string;

    private bubbles;
   
    constructor(private sanitizer: DomSanitizer){
      this.bubbles = [];
      if(!this.chartHeight)
        this.chartHeight=250; 
    }

    ngOnInit() {
    }

    ngOnDestroy() {
      this.destroyBubleChart();
    }

    ngOnChanges(changes: SimpleChanges) {
        if (this.oldQuestion != this.question) {
            this.oldQuestion = this.question;
            this.initialized = false;
        }
        this.destroyBubleChart();
        this.buildChart();

        // setup equation in LaTeX
        setTimeout(function() {
          MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
        }, 50);
    }

    // function to build charts
    private buildChart() {
      if ( !this.initialized ) {
        let chart = this.question.match(new RegExp(/[^{}]+(?=\}%%)/g));
        if (chart['0'].indexOf('type:') >= 0)
          this.chartType =
            parseFloat(chart['0'].match(new RegExp(/type:([^;]*)(?=(;|$))/g))['0'].replace('type:', ''));
        if (chart['0'].indexOf('value:') >= 0)
          this.chartValue =
            parseFloat(chart['0'].match(new RegExp(/value:([^;]*)(?=(;|$))/g))['0'].replace('value:', ''));
        if (chart['0'].indexOf('max:') >= 0) {
          this.chartMaxValue =
            parseFloat(chart['0'].match(new RegExp(/max:([^;]*)(?=(;|$))/g))['0'].replace('max:', ''));
        }
        if (chart['0'].indexOf('main-color:') >= 0) {
          this.mainColor = chart['0'].match(new RegExp(/main-color:([^;]*)(?=(;|$))/g))['0'].replace('main-color:', '');
        }
        if (chart['0'].indexOf('selected-color:') >= 0) {
          this.selectedColor = chart['0'].match(new RegExp(/selected-color:([^;]*)(?=(;|$))/g))['0'].replace('selected-color:', '');
        }
        if (chart['0'].indexOf('stroke-color:') >= 0) {
          this.strokeColor = chart['0'].match(new RegExp(/stroke-color:([^;]*)(?=(;|$))/g))['0'].replace('stroke-color:', '');
        }
        if (chart['0'].indexOf('control:') >= 0)
          this.chartControl = parseFloat(chart['0'].match(new RegExp(/control:([^;]*)(?=(;|$))/g))['0']
            .replace('control:', ''));
        this.initialized = true;
      }
      let chartHtml = this.question.replace(new RegExp(/%%chart(.*)(?=%)%/g), "");
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
          chartHtml += '<rect id="rect1" style="y: '+(1 - this.chartValue)*this.chartHeight+'; height:'+
            this.chartValue*this.chartHeight +' !important; width: 100%;';
          chartHtml += ' fill: ' + this.selectedColor + '; stroke: ' + 
          this.strokeColor + '; stroke-width: '+ this.strokeWidth + '"';
          chartHtml +=  '></rect>';
          chartHtml += '</svg>';
          this.chart = this.sanitizer.bypassSecurityTrustHtml(chartHtml);
          break;
        case 2:
          // Chart (type 2 - circle)
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
            if(this.bubbles[i] == undefined){
              this.bubbles[i] = {
                x: Math.random() * (canvas.width-this.bubleRadius*2),
                y: Math.random() * (canvas.height-this.bubleRadius*2),
                radius: this.bubleRadius
              };
            }
          }
          this.bubleChartRebuildFunctionId = setInterval(() => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            this.drawBublesChart(this.chartValue, this.chartMaxValue, ctx, canvas);
          }, 80);
          break;
      }
    }

    // function to draw Bubles Chart
    private drawBublesChart(bublesPercent: number, 
      maxBublesNum: number, ctx: CanvasRenderingContext2D,
      canvas: HTMLCanvasElement) {
      let bublesNum = Math.round(maxBublesNum*bublesPercent);
      for (let i = 0; i < bublesNum; i++) {
        this.bubbles[i] = this.drawBuble(2, ctx, canvas, this.bubbles[i]);
      }
      for (let i = bublesNum; i < maxBublesNum; i++) {
        this.bubbles[i] = this.drawBuble(1, ctx, canvas, this.bubbles[i]);
      }
    }

    // function to draw one Buble
    private drawBuble(type: number, ctx: CanvasRenderingContext2D,
      canvas:HTMLCanvasElement, buble) {
      ctx.strokeStyle = this.strokeColor;
      ctx.lineWidth   = this.strokeWidth;
      if(type == 1) {
        ctx.fillStyle = this.mainColor;
      }
      if(type == 2) {
        ctx.fillStyle = this.selectedColor;
      }
      buble.x += Math.random() * 2 - 1;
      buble.y += Math.random() * 2 - 1;

      // Check if buble goes beyond the field
      let bubleDiameter = buble.radius*2;
      if(buble.x > canvas.width-bubleDiameter)
        buble.x = canvas.width-bubleDiameter;
      if(buble.x < bubleDiameter)
        buble.x = bubleDiameter;
      if(buble.y > canvas.height-bubleDiameter)
        buble.y = canvas.height-bubleDiameter;
      if(buble.y < bubleDiameter)
        buble.y = bubleDiameter;

      ctx.beginPath();
      ctx.arc(buble.x, buble.y, buble.radius, 0, Math.PI*2, true);
      ctx.fill();
      ctx.stroke();
      return buble;
    }

    // remove buble chart rebuild function if it exists
    private destroyBubleChart() {
      if (this.bubleChartRebuildFunctionId)
        clearInterval(this.bubleChartRebuildFunctionId); 
    }
}