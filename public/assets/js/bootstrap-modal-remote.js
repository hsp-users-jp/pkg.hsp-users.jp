/* ----------------------------------------------------------
 *  Bootstrap Modal plugin remote option compatible script
 *    for Bootstrap v3.3 or later
 *  Copyright (c) 2015 sharkpp All rights reserved.
 *  This source code is under the MIT License.
 */
(function ($) {
    "use strict";
    $('a[data-toggle="modal"]')
        .on('click',function(){
            var id = $(this).attr('data-target');
            if (!$(id).length)
                $('<div id="' + id.replace('#','') + '" class="modal fade" role="dialog" />')
                    .appendTo('body');
            $(id).empty();
            $.get($(this)
                .attr('href'), function(data) {
                        $(id)
                            .empty()
                            .append($(data))
                            .modal();
                    });
            return false;
        });
}(window.jQuery));
