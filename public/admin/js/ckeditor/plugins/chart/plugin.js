CKEDITOR.plugins.add( 'chart', {
    icons: 'chart',
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
                        label : 'Settings',
                        elements :
                        [
                            {
                                type : 'select',
                                id : 'type',
                                label : 'Chose chart type',
                                items : [ 
                                    [ 'Type 1 (Rectangle)', '1' ],
                                    [ 'Type 2 (Pie)', '3' ],
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
                            }
                        ]
                    }
                ],
                onOk : function()
                {
                    var dialog = this,
                        data = {}
                    this.commitContent( data );
                    let chartHtml = '%%chart{type:'+data.type+'; value:'+data.value;
                    if (data.type == 3) {
                        if(data.max < 1) data.max = 1;
                        chartHtml += '; max:'+data.max;
                    }
                    chartHtml += '}%%';
                    editor.insertHtml(chartHtml);
                }
            };
        });
    }
});