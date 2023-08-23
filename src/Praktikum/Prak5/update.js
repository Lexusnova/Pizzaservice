function process(pizzalist) {
  "use strict";
  if (!isRealValue(pizzalist)) {
    let para = document.createElement('p');
    para.innerText = "Sie haben noch keine Pizza bestellt.";
    document.getElementById('customerDiv').appendChild(para);
    
    para = document.createElement('p');
    para.innerText = "Zum Bestellen von Pizzen bitte klicken Sie den Button.";
    document.getElementById('customerDiv').appendChild(para);
    return; // don't do the loop with undefined values.
  }
  
  pizzalist = JSON.parse(pizzalist);
  const customerDiv = document.getElementById('customerDiv');
  // empty div first otherwise it will just append new childs without removing/changing the old ones
  while (customerDiv.firstChild) {
    customerDiv.removeChild(customerDiv.firstChild);
  }
  pizzalist.forEach((pizza) => {
    let para = document.createElement('p');
    para.innerText = pizza.name + ": " + processStatus(pizza.status);
    customerDiv.appendChild(para);
  });
};
function isRealValue(obj) {
  return obj && obj !== 'null' && obj !== 'undefined';
}
function processStatus(status) {
  if(status == 1) return "bestellt";
  if(status == 2) return "im Ofen";
  if(status == 3) return "fertig";
  if(status == 4) return "unterwegs";
  if(status == 5) return "geliefert";
  return "undefined";
}
// XMLHttpRequest for customerPageData
// request als globale Variable anlegen (haesslich, aber bequem)
var request = new XMLHttpRequest(); 
function requestData() { // fordert die Daten asynchron an
  request.open("GET", "CustomerStatus.php"); // URL für HTTP-GET
  request.onreadystatechange = processData; //Callback-Handler zuordnen
  request.send(null); // Request abschicken
}
function processData() {
  if(request.readyState == 4) { // Uebertragung = DONE
     if (request.status == 200) {   // HTTP-Status = OK
       if(request.responseText != null) 
         process(request.responseText);// Daten verarbeiten
       else console.error ("Dokument ist leer");        
     } 
     else console.error ("Uebertragung fehlgeschlagen");
  } else ;          // Uebertragung laeuft noch
}
setInterval(requestData, 2000);