

function maj_formation(oSelect){
  //console.log(oSelect.getElementsByTagName('option'));
  var formation_selectionne = oSelect.options[oSelect.selectedIndex].value;
  $.ajax({
    dataType : 'json',
    url: 'index.php',
    type: 'GET',
    data: {
      formSelectionee : formation_selectionne
    },
    success : function(reponse){
        readDataFormationEtudiant(reponse);
      },
    error: function(reponse){
      console.log(reponse,"error");
    }
  });
  //oSelect.disabled="disabled";

  $.ajax({
    dataType : 'json',
    url: 'index.php',
    data: {
      formSelectionee2 : formation_selectionne
    },
    success : function(reponse){
      readDataFormationEntreprise(reponse);
    },
    error : function(reponse){
      console.log(reponse,"error");
    }
  });
}

function maj_entreprise(oSelect){
  var entreprise_selectionne = oSelect.options[oSelect.selectedIndex].value;
  var formation_selectionne = document.getElementById("formation_ajout_etudiant").options[document.getElementById("formation_ajout_etudiant").selectedIndex].value;
  $.ajax({
    dataType : 'json',
    url : 'index.php',
    data : {
      entreprise_selectionne : entreprise_selectionne,
      formation_selectionne : formation_selectionne
    },
    success : function(reponse){
      //console.log(reponse)
      readDataEntrepriseEtudiant(reponse);
    },
    error : function(reponse){
      console.log(reponse, "error");
    }
  })
}

function readDataFormationEtudiant(oData){
  //met à jour la liste étudiante
  var oSelectEtudiant = document.getElementById("nom_etu_ajout_planning");
  var oOptsEtu = oSelectEtudiant.getElementsByTagName("option");
  oOptsEtu[0].selected = 'selected';
  var M = new Array();
  $.each(oData, function(i, item){
    M.push(item[0]);
  })
  $.each(oOptsEtu, function(i, item){
    if (item.style.color == 'black'){
      item.style.display = 'block';
      item.style.color = 'black'
      oSelectEtudiant.disabled = "disabled";
    }
    if(item.style.display != 'none'){
      if(!M.includes(oOptsEtu[i].value)){
        item.style.display = 'none';
        item.style.color = 'black'
      }
      else{
        item.style.display = 'block'
      }
    }
  })
}
  //met à jour la liste entreprise
function readDataFormationEntreprise(oData){
  var oSelectEntreprise = document.getElementById("entreprise_ajout_etudiant_planning");
  var oOptsEntreprise = oSelectEntreprise.getElementsByTagName("option");
  oOptsEntreprise[0].selected = 'selected';
  var M = new Array();
  $.each(oData, function(i, item){
    M.push(item.Identreprise);
  })
  $.each(oOptsEntreprise, function(i, item){
    if (  item.style.color == 'black'){
      item.style.display = 'block';
    }
    if(item.style.display != 'none'){
      if (!M.includes(oOptsEntreprise[i].value)){
        item.style.display = 'none';
        item.style.color = 'black'
      }
      else{
        item.style.display = 'block';
      }
    }
  })
  oSelectEntreprise.disabled = false;
}

function readDataEntrepriseEtudiant(oData){
  var oSelectEtudiant = document.getElementById("nom_etu_ajout_planning");
  var oOptsEtu = oSelectEtudiant.getElementsByTagName("option");
  oOptsEtu[0].selected = 'selected';
  var M = new Array();
  $.each(oData, function(i, item){
    M.push(item.Idetudiant);
  })
  $.each(oOptsEtu, function(i, item){
    if (  item.style.color == 'black'){
      item.style.display = 'block';
    }
    if(item.style.display != 'none'){
      if(!M.includes(oOptsEtu[i].value)){
        item.style.display = 'none';
        item.style.color = 'black'
      }
      else{
        //console.log(oOptsEtu[i].value);
        item.style.display = 'block'
      }
    }
  })
  oSelectEtudiant.disabled = false;
}

function maj_heure_suppression(oSelect){
  var num_creneau = oSelect.options[oSelect.selectedIndex].value;
  $.ajax({
    dataType : 'json',
    url : 'index.php',
    data : {
      num_creneau : num_creneau
    },
    success : function(reponse){
      //console.log(reponse)
      readDataHeureEtudiantSuppr(reponse);
      console.log(reponse);
    },
    error : function(reponse){
      console.log(reponse, "error");
    }
  })
}
function readDataHeureEtudiantSuppr(oData){
  var oSelectEtudiant = document.getElementById("nom_etu_suppr_planning");
  var oOptsEtu = oSelectEtudiant.getElementsByTagName("option");
  oOptsEtu[0].selected = 'selected';
  var M = new Array();
  $.each(oData, function(i, item){
    M.push(item.Idetudiant);
  })
  $.each(oOptsEtu, function(i, item){
    if (  item.style.color == 'green'){
      item.style.display = 'block';
      item.style.color = "black";

    }
    if(item.style.display != 'none'){
      if(!M.includes(oOptsEtu[i].value)){
        item.style.display = 'none';
        item.style.color = "green";
      }
      else{
        //console.log(oOptsEtu[i].value);
        item.style.display = 'block'
      }
    }
  })
}
