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

            var name = this.options.name || 'default_name';
            var escape = typeof this.options.escape == "undefind" ? true : this.options.escape;

            for(var i=0; i<this.sourceData.length; i++) {
                $label = $('<label class="radio-inline">')
                    .append($('<input>', {
                        type: 'radio',
                        name: name,
                        value: this.sourceData[i].value
                    }));
                escape ? $label.append($('<span>').text(this.sourceData[i].text))
                       : $label.append($('<span>').html(this.sourceData[i].text));

                // Add radio buttons to template
                this.$tpl.append($('<div>').append($label));
            }

            this.$input = this.$tpl.find('input[type="radio"]');
            this.setClass();
        },

        value2str: function(value) {
            return typeof(value) != 'undefined' ? value : '';
        },

        //parse separated string
        str2value: function(str) {
            return typeof(str) != 'undefined' ? str : null;
        },

        //set checked on required radio buttons
        value2input: function(value) {
            this.$input.prop('checked', false);
            this.$input.each(function(i, el) {
                var val = $(el).val()
                if (val == value) {
                    $(el).prop('checked', true);
                }
            });
        },

        input2value: function() {
            return this.$input.filter(':checked').val();
        },

        //collect text of checked boxes
        value2htmlFinal: function(value, element) {
            var checked = $.fn.editableutils.itemsByValue(value, this.sourceData);
            if(checked.length) {
                var textual_value = this.sourceData.filter(
                    function(x) {
                        if (x.value == value) return x.text
                    }
                )[0].text;
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
        tpl:'<div class="editable-radiolist"></div>',

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