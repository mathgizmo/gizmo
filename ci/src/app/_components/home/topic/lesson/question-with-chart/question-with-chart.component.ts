import { Component, Inject, OnInit, OnDestroy, 
    ChangeDetectionStrategy, Input, OnChanges, SimpleChanges } from '@angular/core';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';

@Component({
    selector: 'question-with-chart',
    template: `
      <h2 id="chart-container" [innerHTML]="chart"></h2>
      <mat-form-field id="controls" (change)="ngOnChanges()">
        <input matInput placeholder="Value" [(ngModel)]="chartValue" 
        type="number" step="0.1" max="1" min="0">
      </mat-form-field>
    `,
    styles: [`
        #chart-container{
          display: flex;
          align-items: center;
          justify-content: center;
          flex-direction: column;
          margin: 0;
          padding: 0;
        }
        #controls {
          margin: 5px;
          padding: 0;
        }
    `],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class QuestionWithChartComponent implements OnInit, OnDestroy, OnChanges {
    
    @Input() question: string;
    @Input() mainColor: string;
    @Input() selectedColor: string;
    @Input() strokeColor: string;
    @Input() chartHeight: number; // dimension of chart area in px
    @Input() strokeWidth: number;

    private bubleRadius: number = 4;
    chart: SafeHtml;
    private bubleChartRebuildFunctionId; // id of function which rebuild buble chart

    private chartType: number;
    private chartValue: number;
    private chartMaxValue: number;
   
    constructor(private sanitizer: DomSanitizer){
      // set default styles if styles are not defined
      if(!this.chartHeight)
        this.chartHeight=250; 
      if(!this.mainColor)
        this.mainColor="#f7f7f7";
      if(!this.selectedColor)
        this.selectedColor="#ff4444"; 
      if(!this.strokeColor)
        this.strokeColor="#111";
      if(!this.strokeWidth)
        this.strokeWidth=1;
    }

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
      this.chartType = 
        +chart['0'].match(/type:([0-9]+)(?=;)/g)['0'].replace('type:', '');
      if (chart['0'].indexOf('max:') >= 0) {
        if(this.chartValue == undefined) this.chartValue = 
          +chart['0'].match(/value:(.*)(?=;)/g)['0'].replace('value:', '');
        this.chartMaxValue = 
          +chart['0'].match(/max:(.*)/g)['0'].replace('max:', '');
      } else {
        if(this.chartValue == undefined) this.chartValue = 
          +chart['0'].match(/value:(.*)/g)['0'].replace('value:', '');
      }
      let chartHtml = this.question.replace(/%%chart(.*)(?=%)%/g, "");
      switch (this.chartType) {
        case 1:
          // Chart (type 1 - rectangle)
          chartHtml += '<svg style="height: '
            + this.chartHeight + '; width:' + this.chartHeight + ';">';
          chartHtml += '<rect id="rect2" style="height:'
            + this.chartHeight +' !important; width: 100%;';
          chartHtml += ' fill: ' + this.mainColor + '; stroke: ' + 
            this.strokeColor + '; stroke-width: '+ this.strokeWidth + '"';
          chartHtml +=  '></rect>';
          chartHtml += '<rect id="rect1" style="height:'+ 
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
          // Chart (type 3 - points)
          document.getElementById('chart-container').innerHTML = chartHtml;
          let canvas = document.createElement("canvas");
          document.getElementById('chart-container').appendChild(canvas);
          canvas.style.height = '100%';
          canvas.style.width = '100%';
          let ctx = canvas.getContext("2d"); 
          let bubbles = [];
          for(let i = 0; i < this.chartMaxValue; i++) {
            bubbles[i] = {
              x: Math.random() * (canvas.width-this.bubleRadius*2),
              y: Math.random() * (canvas.height-this.bubleRadius*2),
              radius: this.bubleRadius
            };
          }
          this.bubleChartRebuildFunctionId = setInterval(() => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            this.drawBublesChart(this.chartValue, this.chartMaxValue, ctx, canvas, bubbles);
          }, 80);
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
      //console.log(buble);

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