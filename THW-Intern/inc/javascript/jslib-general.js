 <!-- jslib-general.js  Version 1.1
// Diese JS-lib stellt diverse allgemeine Funktionen zur Nutzung auf Webseiten bereit
// Dieser Kommentar verhindert auch das Abspacken bei älteren Browsern.
//
// erstellt von Roland Diera am 07.06.2003 und zuletzt geändert am 02.07.2003
//
// Known Problems:
//   keine
//

// Gibt Datum der letzen Änderung des angezeigten html-Dokumentes an
// funktioniert nur bei statischen html-Seiten (physisch)
// Die Funktion erkennt, ob das lokale, anzeigende System auf UNIX oder Windows
// basiert und gibt das Datum stets im Format TT.MM.JJJJ zurück bzw. aus
function getDocdate(printflag) {
  prefix = 'zuletzt geändert: '
  datum = document.lastModified
  if (datum.substr(3,1) == ",") {
    months = "JanFebMarAprMayJunJulAugSepOctNovDec"
    tag = datum.substr(6,1);
    if (tag < 10) tag = "0" + tag
    monat = (months.indexOf("" + datum.substr(8,3) + "") / 3) + 1
    if (monat < 10) monat = "0" + monat
    jahr = datum.substr(12,4);
  }
  else {
    tag = datum.substr(3,2);
    monat = datum.substr(0,2);
    jahr = datum.substr(6,4);
  }
  datum = tag + '.' + monat + '.' + jahr
  if (printflag) {
    document.write(prefix + datum)
    return true
  }
  else {
    return datum
  }
}

// Informationen über verwendeten Browser abfragen
// Parameter :
//   type  =  steuert Ausgabe ( 0 = Alertbox;  1 = document.writeln() )
function getBrowserinfo(type) {
  var BrowserName = navigator.appName;
  var BrowserVersion = navigator.appVersion;
  msg1 = "Browser: " + BrowserName
  msg2 = "Version: " + BrowserVersion
  if (type == "0") {
    alert(msg1 + "\n" + msg2)
  }
  if (type == "1") {
    document.writeln(msg1 + "<br>")
    document.writeln(msg2)
  }
  return true
}

// Ende der jslib-general.js-->
