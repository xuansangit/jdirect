/* Copyright (C) YOOtheme GmbH, http://www.gnu.org/licenses/gpl.html GNU/GPL */

(function(d){var e=function(){};d.extend(e.prototype,{name:"AliasEdit",options:{url:"index.php?option=com_zoo&controller=manager&format=raw&task=getalias",force_safe:!1,edit:!1},initialize:function(c,b){this.options=d.extend({},this.options,b);var a=this;this.input=c;this.trigger=c.find("a.trigger");this.panel=c.find("div.panel");this.text=this.panel.find("input:text");this.name=c.find('input[name="name"]');this.options.edit||this.name.bind("blur.name",function(){a.name.val().length&&!a.text.val().length&&
a.setAlias(a.name.val())});this.trigger.bind("click",function(b){b.preventDefault();d(this).hide();a.panel.addClass("active");a.text.focus();a.text.bind("keydown",function(b){b.stopPropagation();13==b.which&&a.setAlias(a.text.val());27==b.which&&a.remove()});a.input.find("input.accept").bind("click",function(b){b.preventDefault();a.setAlias(a.text.val())});a.input.find("a.cancel").bind("click",function(b){b.preventDefault();a.remove()})})},setAlias:function(c){var b=this;c.length||(c=b.name.val());
d.getJSON(this.options.url,{name:c,force_safe:this.options.force_safe?1:0},function(a){a.length||(a="42");b.text.val(a);b.trigger.text(a);d(b).unbind("blur.name");b.remove()})},remove:function(){this.trigger.show();this.panel.removeClass("active")}});d.fn[e.prototype.name]=function(){var c=arguments,b=c[0]?c[0]:null;return this.each(function(){var a=d(this);if(e.prototype[b]&&a.data(e.prototype.name)&&"initialize"!=b)a.data(e.prototype.name)[b].apply(a.data(e.prototype.name),Array.prototype.slice.call(c,
1));else if(!b||d.isPlainObject(b)){var f=new e;e.prototype.initialize&&f.initialize.apply(f,d.merge([a],c));a.data(e.prototype.name,f)}else d.error("Method "+b+" does not exist on jQuery."+e.name)})}})(jQuery);