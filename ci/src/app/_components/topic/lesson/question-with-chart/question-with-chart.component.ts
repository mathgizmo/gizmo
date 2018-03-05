import { Component, Inject, OnInit, OnDestroy, 
    ChangeDetectionStrategy, Input, OnChanges, SimpleChanges } from '@angular/core';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';

@Component({
    selector: 'question-with-chart',
    template: `<h2 id="chart-container" [innerHTML]="chart"></h2>`,
    styles: [`
        #chart-container{
          display: flex;
          align-items: center;
          justify-content: center;
          flex-direction: column;
          margin: 0;
          padding: 0;
        }
    `],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class QuestionWithChartComponent implements OnInit, OnDestroy, OnChanges {
    
    @Input() question: string;

    chart: SafeHtml;
    chart_height: number = 250; // dimension of chart area in px
    private bubleChartRebuildFunctionId; // id of function which rebuild buble chart
   
    constructor(private sanitizer: DomSanitizer){}

    ngOnInit() {
      this.buildChart();
    }

    ngOnDestroy() {
      this.destroyBubleChart();
    }

    ngOnChanges(changes: SimpleChanges) {
        this.destroyBubleChart();
        this.buildChart();
    }

    // function to build charts
    /**
      - type 1 (A1): 
      %%chart{type:1; value:0.3}%% 

      - type 2 (A4):
      %%chart{type:2; value:0.3}%% 

      - type 3 (A5):
      %%chart{type:3; value:0.3; max: 10}%% 

      value: percent fill (0-1)
      max: max value
    */
    private buildChart() {
      let chart = this.question.match(/[^{}]+(?=\}%%)/g);
      let chart_type = 
        +chart['0'].match(/type:([0-9]+)(?=;)/g)['0'].replace('type:', '');
      let chart_value, chart_max_value;
      if (chart['0'].indexOf('max:') >= 0) {
        chart_value = 
          +chart['0'].match(/value:(.*)(?=;)/g)['0'].replace('value:', '');
        chart_max_value = 
          +chart['0'].match(/max:(.*)/g)['0'].replace('max:', '');
      } else {
        chart_value = 
          +chart['0'].match(/value:(.*)/g)['0'].replace('value:', '');
      }
      let chartHtml = this.question.replace(/%%chart(.*)(?=%)%/g, "");
      switch (chart_type) {
        case 1:
          // Chart (type 1 - rectangle)
          chartHtml += '<svg style="height: '
            + this.chart_height + '; width:' + this.chart_height + ';">';
          chartHtml += '<rect id="rect2" style="height:'
            + this.chart_height +' !important; width: 100%;"></rect>'
          chartHtml += '<rect id="rect1" style="height:'+ 
            chart_value*this.chart_height +' !important; width: 100%;"></rect>';
          chartHtml += '</svg>';
          this.chart = this.sanitizer.bypassSecurityTrustHtml(chartHtml);
          break;
        case 2:
          // Chart (type 2 - circle)
          let radius = this.chart_height/2;
          let angle = 2*Math.PI*chart_value;
          let x = radius + radius*Math.sin(angle);
          let  y = radius - radius*Math.cos(angle);
          chartHtml += '<svg style="height: '
            + this.chart_height + '; width:' + this.chart_height + ';">';
          if(chart_value < 0.99999) {
            chartHtml += '<circle id="circle2" style="r: ' + radius 
              + ' !important; cx: '+ radius + ' !important; cy: ' 
              + radius + ' !important;"/>';
            chartHtml += '<path id="circle1" d="M'+ radius +','+ radius 
              + ' L' + radius + ',0 A' + radius + ',' + radius;
            if(chart_value <= 0.5){
              chartHtml += ' 1 0,1';
            } else {
              chartHtml += ' 1 1,1';  
            }
            chartHtml += ' ' + x + ', ' + y +' z"></path>'; 
          } else {
            chartHtml += '<circle id="circle1" style="r: ' + radius 
              + ' !important; cx: '+ radius + ' !important; cy: ' 
              + radius + ' !important;"/>';
          }
          chartHtml += '</svg>';
          this.chart = this.sanitizer.bypassSecurityTrustHtml(chartHtml);
          break;
        case 3:
          // Chart (type 3 - points)
          document.getElementById('chart-container').innerHTML = chartHtml;
          let canvas = document.createElement("canvas");
          document.getElementById('chart-container').appendChild(canvas);
          canvas.style.height = '100%';
          canvas.style.width = '100%';
          let ctx = canvas.getContext("2d"); 
          let bubbles = [];
          let bubleRadius = 4;
          for(let i = 0; i < chart_max_value; i++) {
            bubbles[i] = {
              x: Math.random() * (canvas.width-bubleRadius*2),
              y: Math.random() * (canvas.height-bubleRadius*2),
              radius: bubleRadius
            };
          }
          this.bubleChartRebuildFunctionId = setInterval(() => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            this.drawBublesChart(chart_value, chart_max_value, ctx, canvas, bubbles);
          }, 200);
          break;
        default:
          break;
      }
    }

    // function to draw Bubles Chart
    private drawBublesChart(bublesPercent: number, 
      maxBublesNum: number, ctx: CanvasRenderingContext2D,
      canvas: HTMLCanvasElement, bubles) {
      let bublesNum = Math.round(maxBublesNum*bublesPercent);
      for (let i = 0; i < bublesNum; i++) {
        bubles[i] = this.drawBuble(2, ctx, canvas, bubles[i]);
      }
      for (let i = bublesNum; i < maxBublesNum; i++) {
        bubles[i] = this.drawBuble(1, ctx, canvas, bubles[i]);
      }
    }

    // function to draw one Buble
    private drawBuble(type: number, ctx: CanvasRenderingContext2D,
      canvas:HTMLCanvasElement, buble) {
      if(type == 1) {
        ctx.fillStyle = "#111";
      }
      if(type == 2) {
        ctx.fillStyle = "#66cccc";
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
      //console.log(buble);

      ctx.beginPath();
      ctx.arc(buble.x, buble.y, buble.radius, 0, Math.PI*2, true);
      ctx.fill();
      return buble;
    }

    // remove buble chart rebuild function if it exists
    private destroyBubleChart() {
      if (this.bubleChartRebuildFunctionId)
        clearInterval(this.bubleChartRebuildFunctionId); 
    }
}