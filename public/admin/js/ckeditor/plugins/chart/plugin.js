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
                                label : 'Value',
                                'default' : '0',
                                validate : CKEDITOR.dialog.validate
                                    .regex( /([0-9]+([.][0-9]*)?|[.][0-9]+)/, 
                                        "Value must be a real number" ),
                                required : true,
                                commit : function( data )
                                {
                                    data.value = this.getValue();
                                },
                                onChange : function( api ) {
                                    let dialog = CKEDITOR.dialog.getCurrent();
                                    let max = dialog.getContentElement('general', 
                                        'max');
                                    let maxValue = parseFloat(max.getValue());
                                    let thisValue = parseFloat(this.getValue());
                                    if(max.isEnabled()) {
                                        if (thisValue > maxValue) {
                                            alert("Value ("+thisValue
                                                +") must be less than Max Value ("+maxValue+")");
                                        } 
                                    }
                                },
                            },
                            {
                                type : 'text',
                                id : 'max',
                                label : 'Max Value',
                                validate : CKEDITOR.dialog.validate
                                    .regex( /([0-9]+([.][0-9]*)?|[.][0-9]+)/, 
                                        "Max Value must be a real number" ),
                                required : false,
                                commit : function( data )
                                {
                                    data.max = this.getValue();
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
                                commit : function( data )
                                {
                                    data.step = this.getValue();
                                }
                            },
                            {
                                type : 'text',
                                id : 'start',
                                label : 'Start Value',
                                validate : CKEDITOR.dialog.validate
                                    .regex( /([0-9]+([.][0-9]*)?|[.][0-9]+)/, 
                                        "Start Value must be a real number" ),
                                required : false,
                                'default' : '0',
                                commit : function( data )
                                {
                                    data.start = this.getValue();
                                }
                            },
                            {
                                type : 'text',
                                id : 'end',
                                label : 'End Value',
                                validate : CKEDITOR.dialog.validate
                                    .regex( /([0-9]+([.][0-9]*)?|[.][0-9]+)/, 
                                        "End Value must be a real number" ),
                                required : false,
                                'default' : '5',
                                commit : function( data )
                                {
                                    data.end = this.getValue();
                                }
                            }
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
                    chartHtml += 'value:'+data.value+'; ';

                    // set data
                    if(data.type == 4) {
                        chartHtml += 'start-value:'+data.start+'; ';
                        chartHtml += 'end-value:'+data.end+'; ';
                        chartHtml += 'step:'+data.step+'; ';
                    } else {
                        if (data.type == 1 || data.type == 2 || data.type == 3) {
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
                        let max = this.getContentElement('general', 'max');
                        let step = this.getContentElement('general', 'step');
                        
                        // disable unused fields
                        if(type == 4) {
                            this.getContentElement('general', 'start').enable();
                            this.getContentElement('general', 'end').enable();
                            this.getContentElement('general', 'max').disable();
                        } else {
                            this.getContentElement('general', 'start').disable();
                            this.getContentElement('general', 'end').disable();
                            this.getContentElement('general', 'max').enable();
                        }

                        // set defaults
                        if(type == 3) {
                             max.setValue('100');
                        } else {
                            max.setValue('1');
                        }
                        if(max.getValue() <= 1) {
                            step.setValue('0.01');
                        } else {
                            step.setValue('1');
                        }
                        if (type == 4) {
                            step.setValue('0.5');
                        }
                        
                    })
                }
            };
        });

    }
});