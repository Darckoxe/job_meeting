$(function(){
  var cpt = 0;
  var colors = ["red","blue","green","orange","purple","yellow"];
  var taille = colors.length;
  $("#planning td[class=colorMe]").click(function(){
    var nom = $(this).text();
    $("#planning td[class=colorMe]").each(function(){
      if($(this).text() == nom){
        if($(this).css("color") == "rgb(0, 0, 0)"){
          $(this).css("color",colors[cpt]);
        }
        else{
          $(this).css("color","black");
        }
      }
    });
    cpt++;
    if(cpt == taille){
      cpt = 0;
    }
  });
  $("#planning td").hover(function(){
    $(this).css("cursor","pointer");
    $(this).css("font-weight","bold");
  },
  function(){
    $(this).css("font-weight","inherit");
  })
});
