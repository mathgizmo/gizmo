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
                        id : 'general',
                        label : 'General settings',
                        elements :
                        [
                            {
                                type : 'select',
                                id : 'type',
                                label : 'Chart type',
                                items : [ 
                                    [ 'Type 1 (Rectangle)', '1' ],
                                    [ 'Type 2 (Pie)', '2' ],
                                    [ 'Type 3 (Bubles)', '3' ]
                                ],
                                'default' : '1',
                                required : true,
                                commit : function( data )
                                {
                                    data.type = this.getValue();
                                }
                            },
                            {
                                type : 'text',
                                id : 'value',
                                label : 'Percent fill (value between 0 and 1)',
                                'default' : '0.5',
                                validate : CKEDITOR.dialog.validate.regex( /^(0((\.|\,)\d+)?|1((\.|\,)0+)?)$/, "Percent fill must be a number between 0 and 1" ),
                                required : true,
                                commit : function( data )
                                {
                                    data.value = this.getValue();
                                }
                            },
                            {
                                type : 'text',
                                id : 'max',
                                label : 'Max Value (for type 3 only)',
                                validate : CKEDITOR.dialog.validate.integer('Max Value must be a number'),
                                required : false,
                                commit : function( data )
                                {
                                    data.max = this.getValue();
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
                            }
                        ]
                    }
                ],
                onOk : function()
                {
                    var dialog = this,
                        data = {}
                    this.commitContent( data );
                    let chartHtml = '%%chart{type:'+data.type+'; value:'+data.value+'; ';
                    if (data.type == 3) {
                        if(data.max < 1) data.max = 1;
                        chartHtml += 'max:'+data.max + '; ';
                    }

                    data.mainColor = '#'  + 
                        document.getElementById('main-color').value;
                    chartHtml += 'main-color: ' + data.mainColor + '; '; 
                    data.selectedColor = '#'  + 
                        document.getElementById('selected-color').value;
                    chartHtml += 'selected-color: ' + data.selectedColor + '; '; 
                    data.strokeColor = '#'  + 
                        document.getElementById('stroke-color').value;
                    chartHtml += 'stroke-color: ' + data.strokeColor + '; '; 

                    chartHtml += 'control: ' + data.control + '}%%';
                    editor.insertHtml(chartHtml);
                },
                onShow : function() {
                    // If redownload jscolor.js - change this.zIndex (in jscolor.js) 
                    //to 12000 to show color picker over ckeditor dialog!!!
                    CKEDITOR.scriptLoader.load( '../../js/jscolor.js' );
                }
            };
        });

    }
});