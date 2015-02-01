/*!
 *  Bootstrap Modal plugin remote option compatible script
 *    for Bootstrap v3.3 or later
 *
 * @author    sharkpp
 * @copyright sharkpp 2015
 * @license   MIT License
 * @version   1.0.0
 * @requires  jquery, bootstrap
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
