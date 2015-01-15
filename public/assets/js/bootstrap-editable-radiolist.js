/**
 * X-Editable Radiolist extension for Bootstrap 3
 * @requires X-Editable, jquery, etc.
 * @example: 
 
        $('.editable-earn-method').editable({
            name: 'earn_method',
            source: [
                {value: 'swipes', text: 'Number of swipes'},
                {value: 'spend', text: 'Spend Amount ($USD)'}
            ]
        });
 *
 * Adapted by Tomanow
 */
(function($) {
    var Radiolist = function (options) {
        this.init('radiolist', options, Radiolist.defaults);
    };
    $.fn.editableutils.inherit(Radiolist, $.fn.editabletypes.list);

    $.extend(Radiolist.prototype, {
        renderList: function() {
            var $label;
            this.$tpl.empty();
            if(!$.isArray(this.sourceData)) {
                return;
            }

            for(var i=0; i<this.sourceData.length; i++) {
                var name = this.options.name || 'default_name';
                $label = $('<label class="radio-inline">')
                    .append($('<input>', {
                        type: 'radio',
                        name: name,
                        value: this.sourceData[i].value
                    }));
                $label.append($('<span>').text(this.sourceData[i].text));

                // Add radio buttons to template
                this.$tpl.append($label);
            }

            this.$input = this.$tpl.find('input[type="radio"]');
            this.setClass();
        },

        value2str: function(value) {
            return $.isArray(value) ? value.sort().join($.trim(this.options.separator)) : value;
        },

        //parse separated string
        str2value: function(str) {
            var reg, value = null;
            if(typeof str === 'string' && str.length) {
                reg = new RegExp('\\s*'+$.trim(this.options.separator)+'\\s*');
                value = str.split(reg);
            } else if($.isArray(str)) {
                value = str;
            }
            return value;
        },

        //set checked on required radio buttons
        value2input: function(value) {
            this.$input.prop('checked', false);

            if($.isArray(value) && value.length) {
                this.$input.each(function(i, el) {
                    var $el = $(el);
                    // cannot use $.inArray as it performs strict comparison
                    $.each(value, function(j, val) {
                        if($el.val() == val) {
                            $el.prop('checked', true);
                        }
                    });
                });
            }
        },

        input2value: function() {
            return this.$input.filter(':checked').val();
        },

        //collect text of checked boxes
        value2htmlFinal: function(value, element) {
            var checked = $.fn.editableutils.itemsByValue(value, this.sourceData);
            if(checked.length) {
                $(element).html($.fn.editableutils.escape(value));
            } else {
                $(element).empty();
            }
        },

        value2submit: function(value) {
            return value;
        },

        activate: function() {
            this.$input.first().focus();
        }
    });

    Radiolist.defaults = $.extend({}, $.fn.editabletypes.list.defaults, {
        /**
         @property tpl
         @default <div></div>
         **/
        tpl:'<label class="editable-radiolist"></label>',

        /**
         @property inputclass
         @type string
         @default null
         **/
        inputclass: '',

        /**
         Separator of values when reading from `data-value` attribute

         @property separator
         @type string
         @default ','
         **/
        separator: ',',

        name: 'defaultname'
    });

    $.fn.editabletypes.radiolist = Radiolist;

}(window.jQuery));