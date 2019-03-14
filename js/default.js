/*
 * Skrypty użytkownika
 */

$('body').css({ 'background-color': '#fd3' });

$(document).ready(function() {
  $('div.PageContent').fadeIn();
  setTimeout(function() {
    $('.WindowHeader').css({ 'color': '#e50' });
  }, 500);
  $("html, body").animate({ scrollTop: $("#systemList").offset().top - 7 }, 500);
});