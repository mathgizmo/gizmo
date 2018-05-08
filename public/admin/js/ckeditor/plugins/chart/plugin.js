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
                        label : 'Types',
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
                                },
                                onChange : function( api ) {
                                    let type = parseFloat(this.getValue());
                                    let dialog = CKEDITOR.dialog.getCurrent();
                                    let max = dialog.getContentElement('general', 'max');
                                    let step = dialog.getContentElement('general', 'step');
                                    // disable unused fields
                                    if(type == 4) {
                                        dialog.getContentElement('general', 'marks').enable();
                                        dialog.getContentElement('general', 'max').disable();
                                        dialog.getContentElement('optional', 
                                            'mark-diameter').enable();
                                        dialog.getContentElement('optional', 
                                            'point-diameter').enable();
                                    } else {
                                        dialog.getContentElement('general', 'marks').disable();
                                        dialog.getContentElement('general', 'max').enable();
                                        dialog.getContentElement('optional', 
                                            'mark-diameter').disable();
                                        dialog.getContentElement('optional', 
                                            'point-diameter').disable();
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
                                        dialog.getContentElement('optional', 
                                            'stroke-width').setValue('3');
                                    }
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
                            },
                            {
                                type : 'select',
                                id : 'value-display',
                                label : 'Value displayed as:',
                                items : [ 
                                    [ 'Plain Value', '0' ],
                                    [ 'Fraction', '1' ],
                                    [ 'Decimal', '2' ],
                                    [ 'Percentage', '3' ],
                                    [ 'Do not show', '4' ]
                                ],
                                'default' : '0',
                                required : true,
                                commit : function( data )
                                {
                                    data.valueDisplay = this.getValue();
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
                                    let max = dialog.getContentElement('general', 'max');
                                    let maxValue = parseFloat(max.getValue());
                                    let thisValue = parseFloat(this.getValue());
                                    if(max.isEnabled()) {
                                        if (thisValue > maxValue) {
                                            alert("Value ("+thisValue
                                                +") must be less than Max Value ("+maxValue+")");
                                        } 
                                    }
                                }  
                            },
                            {
                                type : 'text',
                                id : 'max',
                                label : 'Max Value',
                                validate : CKEDITOR.dialog.validate
                                    .regex( /([0-9]+([.][0-9]*)?|[.][0-9]+)/, 
                                        "Max Value must be a real number" ),
                                required : false,
                                'default': 1,
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
                                'default': 0.01,
                                commit : function( data )
                                {
                                    data.step = this.getValue();
                                }
                            },
                            {
                                type : 'text',
                                id : 'marks',
                                label : 'Marks List',
                                validate : CKEDITOR.dialog.validate
                                    .regex( /(([0-9]+([.][0-9]*)?|[.][0-9]+)(, *([0-9]+([.][0-9]*)?|[.][0-9]+)))*/, 
                                        "Marks must be a comma separated list of numbers" ),
                                required : false,
                                'default' : '0, 0.5, 1',
                                commit : function( data )
                                {
                                    data.marks = this.getValue();
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
                            },
                            {
                                type : 'text',
                                id : 'mark-diameter',
                                label : 'Mark Diameter:',
                                validate : CKEDITOR.dialog.validate
                                    .integer('Mark Diameter must be a number'),
                                required : false,
                                'default': 8,
                                commit : function( data )
                                {
                                    data.markDiameter = this.getValue();
                                }
                            },
                            {
                                type : 'text',
                                id : 'point-diameter',
                                label : 'Point Diameter:',
                                validate : CKEDITOR.dialog.validate
                                    .integer('Point Diameter must be a number'),
                                required : false,
                                'default': 3,
                                commit : function( data )
                                {
                                    data.pointDiameter = this.getValue();
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
                        chartHtml += 'marks:'+data.marks+'; ';
                    } else {
                        if (data.type == 1 || data.type == 2 || data.type == 3) {
                            chartHtml += 'max:'+data.max + '; ';
                        }
                    }
                    chartHtml += 'step:'+data.step+'; ';
                    
                    // set styles
                    data.mainColor = '#'  + 
                        document.getElementById('main-color').value;
                    chartHtml += 'main-color: ' + data.mainColor.trim() + '; '; 
                    data.selectedColor = '#'  + 
                        document.getElementById('selected-color').value;
                    chartHtml += 'selected-color: ' + data.selectedColor.trim() + '; '; 
                    data.strokeColor = '#'  + 
                        document.getElementById('stroke-color').value;
                    chartHtml += 'stroke-color: ' + data.strokeColor.trim() + '; '; 
                    if(data.strokeWidth > 0) 
                        chartHtml += 'stroke-width:'+data.strokeWidth+'; ';
                    if(data.type == 4) {
                        if(data.markDiameter > 0) 
                            chartHtml += 'mark-diameter:'+data.markDiameter+'; ';
                        if(data.pointDiameter > 0) 
                            chartHtml += 'point-diameter:'+data.pointDiameter+'; ';
                    }

                    // set value display
                    chartHtml += 'value-display:'+data.valueDisplay+'; ';

                    // set control
                    chartHtml += 'control: ' + data.control + '}%%';

                    // insert chart string to editor
                    if(editor.getData().match(new RegExp(/[^{}]+(?=\}%%)/g))) {
                        editor.setData(
                            editor.getData().replace(new RegExp(/%%chart(.*)(?=%)%/g), chartHtml)
                        );
                    }
                    else editor.insertHtml(chartHtml);
                },
                onShow : function() {
                    // load for edit question
                    let load = CKEDITOR.scriptLoader.load( '../../js/jscolor.js' );
                    // load for create question
                    if(!load) CKEDITOR.scriptLoader.load( '../js/jscolor.js' );

                    // If redownload jscolor.js - change this.zIndex (in jscolor.js) 
                    //to 12000 to show color picker over ckeditor dialog!!!

                    // try to parse existing chart string
                    let chartStr = ""+editor.getData()
                        .match(new RegExp(/[^{}]+(?=\}%%)/g));
                    if(chartStr) {
                        if (chartStr.indexOf('type:') >= 0) {
                            this.getContentElement('types', 'type').setValue(
                                parseFloat(chartStr.match(
                                    new RegExp(/type:([^;]*)(?=(;|$))/g))['0']
                                .replace('type:', ''))
                            );
                        } 
                        if (chartStr.indexOf('value:') >= 0) {
                            this.getContentElement('general', 'value').setValue(
                                parseFloat(chartStr.match(
                                    new RegExp(/value:([^;]*)(?=(;|$))/g))['0']
                                .replace('value:', ''))
                            );
                        }
                        if (chartStr.indexOf('max:') >= 0) {
                            this.getContentElement('general', 'max').setValue(
                                parseFloat(chartStr.match(
                                    new RegExp(/max:([^;]*)(?=(;|$))/g))['0']
                                .replace('max:', ''))
                            );
                        }
                        if (chartStr.indexOf('marks:') >= 0) {
                           this.getContentElement('general', 'marks').setValue(
                                chartStr.match(new RegExp(/marks:([^;]*)(?=(;|$))/g))['0']
                                .replace('marks:', '').split(',').map(Number)
                            );
                        }
                        if (chartStr.indexOf('step:') >= 0) {
                            this.getContentElement('general', 'step').setValue(
                                parseFloat(chartStr.match(
                                    new RegExp(/step:([^;]*)(?=(;|$))/g))['0']
                                .replace('step:', ''))
                            );
                        }
                        if (chartStr.indexOf('main-color:') >= 0) {
                            document.getElementById('main-color').value =
                                chartStr.match(new RegExp(/main-color:([^;]*)(?=(;|$))/g))['0']
                            .replace('main-color:', '').replace('#', '').trim();
                        }
                        if (chartStr.indexOf('selected-color:') >= 0) {
                            document.getElementById('selected-color').value =
                                chartStr.match(new RegExp(/selected-color:([^;]*)(?=(;|$))/g))['0']
                            .replace('selected-color:', '').replace('#', '').trim();
                        }
                        if (chartStr.indexOf('stroke-color:') >= 0) {
                            document.getElementById('stroke-color').value = 
                                chartStr.match(new RegExp(/stroke-color:([^;]*)(?=(;|$))/g))['0']
                            .replace('stroke-color:', '').replace('#', '').trim();
                        }
                        if (chartStr.indexOf('stroke-width:') >= 0) {
                            this.getContentElement('optional', 'stroke-width').setValue(
                                chartStr.match(
                                    new RegExp(/stroke-width:([^;]*)(?=(;|$))/g))['0']
                                .replace('stroke-width:', '')
                            );
                        }
                        if (chartStr.indexOf('point-diameter:') >= 0) {
                            this.getContentElement('optional', 'point-diameter')
                                .setValue(chartStr.match(
                                    new RegExp(/point-diameter:([^;]*)(?=(;|$))/g))['0']
                                .replace('point-diameter:', '')
                            );
                        }
                        if (chartStr.indexOf('mark-diameter:') >= 0) {
                            this.getContentElement('optional', 'mark-diameter')
                                .setValue(chartStr.match(
                                    new RegExp(/mark-diameter:([^;]*)(?=(;|$))/g))['0']
                                .replace('mark-diameter:', '')
                            );
                        }
                        if (chartStr.indexOf('control:') >= 0) {
                            this.getContentElement('types', 'control').setValue(
                                parseFloat(chartStr.match(
                                    new RegExp(/control:([^;]*)(?=(;|$))/g))['0']
                                .replace('control:', ''))
                            );
                        }
                        if (chartStr.indexOf('value-display:') >= 0) {
                            this.getContentElement('types', 'value-display').setValue(
                                parseFloat(chartStr.match(
                                    new RegExp(/value-display:([^;]*)(?=(;|$))/g))['0']
                                .replace('value-display:', ''))
                            );
                        }

                        // disable unused fields for default type
                        if(this.getContentElement('types', 'type').getValue() != 4) {
                            this.getContentElement('general', 'marks').disable(); 
                            this.getContentElement('optional', 'mark-diameter').disable();
                            this.getContentElement('optional', 'point-diameter').disable();
                        }
                    } 
                     
                }
                /* this function trigered when tab changed
                onLoad : function() {
                    this.on('selectPage', function (e) {
                        //let type = this.getContentElement('types', 'type').getValue();
                    })
                }
                */
            };
        });

    }
});