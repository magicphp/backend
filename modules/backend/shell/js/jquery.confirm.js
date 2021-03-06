/*!
 * jquery.confirm
 *
 * @version 2.0
 *
 * @author My C-Labs
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 * @author Russel Vela
 *
 * @url http://myclabs.github.io/jquery.confirm/
 */
(function(a){a.fn.confirm=function(b){if(typeof b==="undefined"){b={}}b.button=a(this);this.click(function(c){c.preventDefault();a.confirm(b,c)});return this};a.confirm=function(d,h){var f=a.extend({text:"Are you sure?",title:"",confirmButton:"Yes",cancelButton:"Cancel",post:false,confirm:function(j){var e=h.currentTarget.attributes.href.value;if(d.post){var i=a('<form method="post" class="hide" action="'+e+'"></form>');a("body").append(i);i.submit()}else{window.location=e}},cancel:function(e){},button:null},d);var b="";if(f.title!==""){b='<div class=modal-header><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">'+f.title+"</h4></div>"}var c='<div class="confirmation-modal modal fade" tabindex="-1" role="dialog"><div class="modal-dialog"><div class="modal-content">'+b+'<div class="modal-body">'+f.text+'</div><div class="modal-footer"><button class="confirm btn btn-primary" type="button" data-dismiss="modal">'+f.confirmButton+'</button><button class="cancel btn btn-default" type="button" data-dismiss="modal">'+f.cancelButton+"</div></div></div></div></div>";var g=a(c);g.on("shown",function(){g.find(".btn-primary:first").focus()});g.on("hidden",function(){g.remove()});g.find(".confirm").click(function(){f.confirm(f.button)});g.find(".cancel").click(function(){f.cancel(f.button)});a("body").append(g);g.modal("show")}})(jQuery);