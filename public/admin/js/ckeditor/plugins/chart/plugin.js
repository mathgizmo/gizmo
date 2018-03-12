CKEDITOR.plugins.add( 'chart', {
    init: function( editor ) {
        
        editor.addCommand( 'chartDialog', new CKEDITOR.dialogCommand( 'chartDialog' ) );

        editor.ui.addButton( 'chartButton',
        {
            label: 'Chart',
            command: 'chartDialog',
            icon: this.path + 'icons/chart.png'
        } );

        CKEDITOR.dialog.add( 'chartDialog', function( editor )
        {
            return {
                title : 'Chart Properties',
                minWidth : 350,
                minHeight : 160,
                contents :
                [
                    {
                        id : 'types',
                        label : 'Chart type',
                        elements :
                        [
                            {
                                type : 'select',
                                id : 'type',
                                label : 'Chart type',
                                items : [ 
                                    [ 'Rectangle (Type 1)', '1' ],
                                    [ 'Pie (Type 2)', '2' ],
                                    [ 'Dots (Type 3)', '3' ],
                                    [ 'Slider (Type 4)', '4' ]
                                ],
                                'default' : '1',
                                required : true,
                                commit : function( data )
                                {
                                    data.type = this.getValue();
                                }
                            },
                            {
                                type : 'select',
                                id : 'control',
                                label : 'Control type',
                                items : [ 
                                    [ 'No Control', '0' ],
                                    [ 'Input', '1' ],
                                    [ 'Slider', '2' ]
                                ],
                                'default' : '0',
                                required : true,
                                commit : function( data )
                                {
                                    data.control = this.getValue();
                                }
                            }
                        ]
                    },
                    {
                        id : 'general',
                        label : 'General settings',
                        elements :
                        [
                            {
                                type : 'text',
                                id : 'value',
                                label : 'Percent fill (value between 0 and 1)',
                                'default' : '0.5',
                                validate : CKEDITOR.dialog.validate
                                    .regex( /^(0((\.|\,)\d+)?|1((\.|\,)0+)?)$/, 
                                        "Percent fill must be a number between 0 and 1" ),
                                required : true,
                                commit : function( data )
                                {
                                    data.value = this.getValue();
                                }
                            },
                            {
                                type : 'text',
                                id : 'max',
                                label : 'Max Value',
                                validate : CKEDITOR.dialog.validate
                                    .integer('Max Value must be a number'),
                                required : false,
                                'default' : '10',
                                commit : function( data )
                                {
                                    data.max = this.getValue();
                                }
                            },
                            {
                                type : 'text',
                                id : 'start-value',
                                label : 'Start Value',
                                validate : CKEDITOR.dialog.validate
                                    .regex( /([0-9]+([.][0-9]*)?|[.][0-9]+)/, 
                                        "Start Value must be a real number" ),
                                required : false,
                                'default' : '1',
                                commit : function( data )
                                {
                                    data.startValue = this.getValue();
                                }
                            },
                            {
                                type : 'text',
                                id : 'end-value',
                                label : 'End Value',
                                validate : CKEDITOR.dialog.validate
                                    .regex( /([0-9]+([.][0-9]*)?|[.][0-9]+)/, 
                                        "End Value must be a real number" ),
                                required : false,
                                'default' : '10',
                                commit : function( data )
                                {
                                    data.endValue = this.getValue();
                                }
                            },
                            {
                                type : 'text',
                                id : 'step',
                                label : 'Step',
                                validate : CKEDITOR.dialog.validate
                                    .regex( /([0-9]+([.][0-9]*)?|[.][0-9]+)/, 
                                        "Step must be a real number" ),
                                required : false,
                                'default' : '0.5',
                                commit : function( data )
                                {
                                    data.step = this.getValue();
                                }
                            },
                        ]
                    },
                    {
                        id: 'optional',
                        label: 'Optional settings',
                        elements: [
                            {
                                type : 'html',
                                html: 'Main Color: <input class="jscolor" id="main-color" value="F7F7F7">',
                                required : false,
                            },
                            {
                                type : 'html',
                                html: 'Selected Color: <input class="jscolor" id="selected-color" value="FF4444">',
                                required : false,
                            },
                            {
                                type : 'html',
                                html: 'Stroke Color: <input class="jscolor" id="stroke-color" value="111">',
                                required : false,
                            },
                            {
                                type : 'text',
                                id : 'stroke-width',
                                label : 'Stroke Width:',
                                validate : CKEDITOR.dialog.validate
                                    .integer('Stroke Width must be a number'),
                                required : false,
                                'default': '',
                                commit : function( data )
                                {
                                    data.strokeWidth = this.getValue();
                                }
                            }
                        ]
                    }
                ],
                onOk : function()
                {
                    var dialog = this,
                        data = {}
                    this.commitContent( data );

                    // set type
                    let chartHtml = '%%chart{type:'+data.type+'; ';

                    // set data
                    if(data.type == 4) {
                        chartHtml += 'start-value:'+data.startValue+'; ';
                        chartHtml += 'end-value:'+data.endValue+'; ';
                        chartHtml += 'step:'+data.step+'; ';
                    } else {
                        chartHtml += 'value:'+data.value+'; ';
                        if (data.type == 3) {
                            chartHtml += 'max:'+data.max + '; ';
                        }
                    }
                    
                    // set styles
                    data.mainColor = '#'  + 
                        document.getElementById('main-color').value;
                    chartHtml += 'main-color: ' + data.mainColor + '; '; 
                    data.selectedColor = '#'  + 
                        document.getElementById('selected-color').value;
                    chartHtml += 'selected-color: ' + data.selectedColor + '; '; 
                    data.strokeColor = '#'  + 
                        document.getElementById('stroke-color').value;
                    chartHtml += 'stroke-color: ' + data.strokeColor + '; '; 
                    if(data.strokeWidth > 0) 
                        chartHtml += 'stroke-width:'+data.strokeWidth+'; ';

                    // set control
                    chartHtml += 'control: ' + data.control + '}%%';
                    editor.insertHtml(chartHtml);
                },
                onShow : function() {
                    // load for edit question
                    let load = CKEDITOR.scriptLoader.load( '../../js/jscolor.js' );
                    // load for create question
                    if(!load) CKEDITOR.scriptLoader.load( '../js/jscolor.js' );

                    // If redownload jscolor.js - change this.zIndex (in jscolor.js) 
                    //to 12000 to show color picker over ckeditor dialog!!!
                },
                onLoad : function() {
                    this.on('selectPage', function (e) {
                        let type = this.getContentElement('types', 'type').getValue();
                        if(type == 3) {
                            this.getContentElement('general', 'max').enable();
                        } else this.getContentElement('general', 'max').disable();
                        if(type == 4) {
                            this.getContentElement('general', 'value').disable();
                            this.getContentElement('general', 'start-value').enable();
                            this.getContentElement('general', 'end-value').enable();
                            this.getContentElement('general', 'step').enable();
                        } else {
                            this.getContentElement('general', 'value').enable();
                            this.getContentElement('general', 'start-value').disable();
                            this.getContentElement('general', 'end-value').disable();
                            this.getContentElement('general', 'step').disable();
                        }
                    })
                }
            };
        });

    }
});