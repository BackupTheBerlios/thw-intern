<!-- jslib-forms.js  Version 1.6beta1 commented
// ************************************************************************************************
// Diese JS-lib stellt diverse Funktionen zur Bearbeitung und Überprüfung von
// Formularfeldinhalten bereit. Verwendet aus Kompatibilitätsgründen nur JavaScript-Elemente
// bis JavaScript-Standard 1.2
// Für die Browserseite ergeben sich damit als Mindestvoraussetzung:
//  - MS IE ab Version 4.0  (besser ist aber 5.0)
//  - Netscape ab Version 4.0
//  - keine fundierte Aussage zu anderen Browsern möglich
// Dieser Kommentar verhindert auch das Abspacken bei älteren Browsern ohne JS-Support.
//
// erstellt von Roland Diera am 07.06.2003 und zuletzt geändert am 06.08.2003
//
// Known problems:
//  - mit Konqueror Probleme beim Schließen der erzeugten Alert-Boxen
//    deshalb auch die Browserspezifische dispMsg-Funktion für Fehlermeldungen
//    Wurde mit IE und Konqueror getestet. Netscape, Mozilla, Opera steht noch aus.
//  - Alle String und Character-Vergleichsoperationen werden auf Basis des Latin-1-ISO-8859
//    character sets vorgenommen, damit sollten sich die Prüfungen auf allen Plattformen
//    identisch verhalten. MAC-Browser können dennoch Probleme mit bestimmten Zeichen
//    haben. Siehe hierzu function chkString().
//
// HINWEISE ZUR BENUTZUNG:
// ------------------------------------------------------------------------------------------------
// Diese JavaScript-Bibliothek ist mittels folgender Syntax in den HTML-Quellcode
// auf jeder Seite einzubinden, in der Funktionen aus der lib verwendet werden sollen
//   <script type="text/javascript" src="jslib-forms.js"></script>
// Hinweis: die Angabe des MIME-types mittels type= ist ab HTML 4.0 Pflicht
// Zusätzlich können Sie zur Kennzeichnung der JavaScript-Sprachversion das
// language-Attribut verwenden (z.B. language="JavaScript1.2"). Jedoch gehört dieses
// Attribut zu den missbilligten Attributen und wird auch nicht von allen Browsern
// korrekt interpretiert.
//
// Es kann passieren, dass ein JavaScript, das wie diese lib in einer separaten Datei
// notiert ist, lokal wunderbar funktioniert, aber nach dem Hochladen auf einen
// WWW-Host plötzlich nicht mehr. In diesem Fall verwenden Sie den Mimetype
// 'application/x-javascript' anstelle von 'text/javascript'. Dann sollte es bei den
// meisten Server funktionieren.
//
// Ansonsten gibt die mitgelieferte test_jslib-forms.html zahlreiche Beispiele
// für die Benutzung der hier aufgeführten Funktionen.
//
//
// ************************************************************************************************

// Debug Steuer-Flag
var debug=false
// Globale Variable als Iterationscounter für function getBegindate
var firstiteration=0
// Globale Variable als Iterationscounter für max. Fehlermeldungen
var erriteration=0
// Hier die maximale Iterationszahl für Fehlermeldungen anzeigen
var maxerr=3
// Weitere Variablen für Fehleriteration
var i_oldval = new Array()
var v_oldval = new Array()
// Statische Fehlertexte zentral in einem Array
// Index 1 = deutsch, Index 2 = english
var lang_de = 0
var lang_en = 1
var errmsg = new Array()
errmsg[0] = new Array("Fehler: Feldinhalt ist nicht nummerisch!",
                      "Fehler: Der Wert muss größer als 0 sein!",
                      "Interner Fehler: minval > maxval!",
                      "Fehler: eMail-Adresse syntaktisch falsch!",
                      "Fehler: Wert für Tag (TT) ungültig!",
                      "Fehler: Wert für Monat (MM) ungültig!",
                      "Fehler: Datumsangabe ungültig!",
                      "Fehler: Wert für Stunden HH ungültig!",
                      "Fehler: Wert für Minuten MM ungültig!",
                      "Hinweis: Überprüfen Sie Ihre Jahresangabe.",
                      "Fehler: Unzulässiges Zeichen '",
                      "' an Stelle ",
                      "Fehler: Wert ist ausserhalb des gültigen Bereichs (",
                      "Interner Fehler: unzulässiges Prüflevel (",
                      "Interner Fehler: unbekannter Sprachschlüssel (",
                      "Fehler: Sie müssen eine Uhrzeit angeben!",
                      "jslib-forms.js - Laufzeitfehler:",
                      "Fehler: Endedatum liegt vor/ist gleich dem Anfangsdatum!",
                      "Fehler: Datum darf nicht in der Vergangenheit liegen!",
                      "Bitte mailen Sie diese Meldung an: rollo@bithugo.de")
errmsg[1] = new Array("Error: numeric value expected!",
                      "Error: value must be greater than 0!",
                      "Internal error: minval > maxval!",
                      "Error: Syntax-error in eMail address!",
                      "Error: value for day (dd) incorrect!",
                      "Error: value for month (mm) incorrect!",
                      "Error: this is not a valid date!",
                      "Error: value for hours (hh) incorrect!",
                      "Error: value for minutes (mm) incorrect!",
                      "Warning: check your input (esp. years).",
                      "Error: illegal character '",
                      "' at offset ",
                      "Error: value out of range (",
                      "Internal error: incorrect checking level (",
                      "Internal error: unknown language definition key (",
                      "Error: You must enter a time value!",
                      "jslib-forms.js - Runtime error:",
                      "Error: End timestamp is before/equal with begin timestamp!",
                      "Error: Date may not be in the past!",
                      "Please e-mail this message to: rollo@bithugo.de")
// Letzter Index errmsg: 19
// Hier die Standardsprache für statische Fehlertexte einstellen
lang = lang_de
//lang = lang_en

// Zentrale Fehlerbehandlungsroutine bei Laufzeitfehlern
//window.onerror=showRterror

// rein Interne Funktion für die Behandlung des Fehleriterationscounters
// Diese Funktion wird üblicherweise von anderen JS-Funktionen genutzt und nicht direkt im HTML-Code eingebunden
function getOldvalindex(ofield) {
// Identifizieren wir erst mal das betroffene Formularelement
  elname = ofield.name
  j=0
// Jetzt in der Variable i_oldval den Eintrag für das Formularelement
// und damit dessen Index ermitteln
  while ((j < i_oldval.length) && (!(elname == i_oldval[j]))) {
    j++
  }
// Falls der Index nicht gefunden wurde (also wir am Ende des Arrays sind) muss das Element
// erstmalig neu angelegt werden
  if (j == i_oldval.length) {
    i_oldval.push(elname)
// Im v_oldval-Array einen leeren Eintrag erzeugen
    v_oldval.push("")
  }
  if (debug) alert("Index_oldval("+i_oldval.length+"): " + i_oldval.join(",") +"\nValue_oldval("+v_oldval.length+"): " + v_oldval.join(","))
// Den ermittelten Index im i_oldval-Array zurückliefern
  return j
}

// Funktion zum Setzen der Sprache, ohne diese lib ändern zu müssen
//   Wird idealerweise (wenn nicht deutsch als Standardsprache gewünscht wird)
//   gleich im body-Tag mittels event onLoad="setLanguage(1)" eingebunden.
//   onLoad="setLanguage(0)" macht wenig Sinn, außer der default-Wert wurde auf
//   lang = lang_en gestellt
// Parameter:
//   lng    =  Sprache (0 = deutsch; 1 = english)
function setLanguage(lng) {
  switch(lng.toString().toLowerCase()) {
    case "0":
// 0 = deutsch
      lang = lang_de;
      break;
    case "1":
// 1 = english
      lang = lang_en;
      break;
    default:
// alles andere führt zu einer Fehlermeldung
      handleErr(errmsg[lang][14] + lng + ")")
      return false;
    break;
  }
  return true
}

// Browserabhängige Funktion zum Anzeigen von Fehlermeldungen
// Bei Anzeige mit Konqueror erfolgt FM-Ausgabe in der Statuszeile, sonst in Alertbox
// Parameter:
//   message  =  auszugebende (Fehler-)Meldung
// Diese Funktion wird üblicherweise von anderen JS-Funktionen genutzt und nicht direkt im HTML-Code eingebunden
function dispMsg(message) {
// zunächst mal den Browser des Benutzers ermitteln, falls Konqueror, dann
// die Hinweis-/Fehlermeldung in der Browserstatuszeile anzeigen ...
  if (navigator.appName == "Konqueror") window.status = message
// ... ansonsten ein Alert-Fenster erzeugen und Text dort ausgeben
  else alert(message)
// ToDo:
//   Es sind noch Tests mit verschiedenen weiteren Browsern durchzuführen
//   - Netscape auf MS-Plattform
//   - Netscape auf Linux
//   - Mozilla auf MS
//   - Mozilla auf Linux
//   - Opera auf MS
//   - Opera auf Linux
//   - Opera auf MAC
//   - IE auf MAC
//   (Die Kombination IE auf Linux gibt´s ja zum Glück nicht :-)
  return true
}

// Funktion zur Ausgabe von Fehlermeldungen und Fokussierung des fehlerhaften Formularfeldes
//   Bisher: Bei einem Fehler wird der fehlerhafte Feldinhalt selektiert. Man kann das Feld solange nicht
//   verlassen, bis der Inhalt korrigiert oder gelöscht wurde.
//   Neu: Bei dreimaligen Fehlschlag, das Feld zu verlassen, wird der Feldinhalt automatisch gelöscht, und
//   der Fokus auf das nächste Formularelement gelegt. Die Anzahl der Fehlschläge wird über die globale
//   Variable maxerr gesteuert. (Siehe aber "Zentrale ToDos" am Ende der lib)
// Parameter:
//   formelement = behandelndes Formularelement zwecks Fokussierung
//   msgerr      = auszugebende Fehlermeldung
// Diese Funktion wird üblicherweise von anderen JS-Funktionen genutzt und nicht direkt im HTML-Code eingebunden
function handleErr(formelement,msgerr) {
  if (debug) alert("handleErr (start): erriteration = " + erriteration)
  if (erriteration < maxerr) {
// Iterationscounter erhöhen
    erriteration++
// Übergebene Fehlermeldung ausgeben
    dispMsg(msgerr)
// Fehlerhaftes Formularelement selektieren
    formelement.select()
// Feld fokussieren
    formelement.focus()
  }
  else {
// Wenn der maximal Iterationscounter erreicht ist, diesen zurücksetzen, ...
    erriteration=0
// ... Feldinhalt löschen, ...
    formelement.value = ""
// ... und evtl. Konqueror-FM aus Browserstatuszeile entfernen
    window.status = ""
  }
  if (debug) alert("handleErr (end): erriteration = " + erriteration)
  return true
}

// Zentrale Fehlerfunktion für Laufzeitfehler aller Art
//   Diese Art der Fehlerkontrolle können Sie für Netscape 4.x und
//   MS Internet Explorer ab Version 4.x verwenden. Netscape 6 ist leider nicht
//   in der Lage, die Parameter vollständig zu interpretieren.
//   Er speichert jeweils im ersten Parameter das auslösende Eventobjekt.
//   Opera 5.12 interpretiert den an das Fensterobjekt gebundenen Eventhandler
//   window.onError nicht.
//
//   Achtung: Diese Funktion kann nur logische Fehler überwachen und unterdrücken,
//   die während der Laufzeit der Scripts entstehen. Syntaxfehler, wie z.B. fehlende Klammern
//   usw. sind damit nicht abfangbar.
function showRterror(rterrmsg,file,line) {
   rtmessage = errmsg[lang][16] + "\n"+ rterrmsg + "\n" + file + "\nSource line: " + line + "\n" + errmsg[lang][19]
   alert(rtmessage)
   return true
}

// Focus auf erstes Formularelement setzen, welches den Fokus erhalten kann (also erstes nicht-hidden-Feld)
// Parameter:
//   whichform  =  name des Formularobjektes (z.B. "thisform")
function setFirstfocus(whichform) {
// Das erste Element ermitteln, das den Fokus erhalten kann
  l = 0
// Der Typ des Formularfeldes darf nicht 'hidden' (also versteckt) sein. Sicherheitshalber checken
// wir auch gleich auf 'undefined'
  chktypeerror = ((whichform.elements[l].type == "undefined") || (whichform.elements[l].type == "hidden"))
  while ((chktypeerror == true) && (whichform.elements[l])) {
   	l++
    chktypeerror = ((whichform.elements[l].type == "undefined") || (whichform.elements[l].type == "hidden"))
  }
// Falls das Element existiert (doppelt gecheckt) ...
  if (whichform.elements[l]) {
// ... Fokus auf das ermittelte Formularelement des Formulars 'whichform' setzen, ...
    whichform.elements[l].focus()
// ... dann den Inhalt des Feldes selektieren
    whichform.elements[l].select()
    return true
  }
  return false
}

// setzt den Iterationscounter zurück bei Formularreset und setzt Focus auf erstes Element
// Parameter:
//   whichform  =  name des Formularobjektes (z.B. "thisform")
function doFormreset(whichform) {
// Den Iterationscounter wieder zurücksetzen
  firstiteration=0
  erriteration=0
// Die Funktion setFirstfocus aufrufen
  setFirstfocus(whichform)
  return true
}

// Führende Nullen abschneiden
// Parameter:
//   val  =  Zahl (es muss zwingend ein numerischer Wert übergeben werden, ggf. vorher mit chkNum validieren)
// Diese Funktion wird üblicherweise von anderen JS-Funktionen genutzt und nicht direkt im HTML-Code eingebunden
function truncNum(val) {
  var pVal = val
// Solange das erste Zeichen des nummerischen Strings eine '0' ist, abschneiden
  while ((pVal.charAt(0) == "0") && (pVal.length > 1)) pVal = pVal.substring(1,pVal.length)
  return pVal
}

// Prüft einen String auf unzulässige (Sonder-)Zeichen
// Parameter:
//   strfield  =  Stringfeld
//   level     =  Prüfstufe als String (also z.B. 'none')
//                none     (0) =  Nur Steuerzeichen ausfiltern
//                poor     (1) =  a..z, A..Z, äÄöÖüÜß, Blank, '-', Satzzeichen und weitere Sonderzeichen
//                normal   (2) =  a..z, A..Z, äÄöÖüÜß, Blank und '-'
//                strict   (3) =  a..z, A..Z, äÄöÖüÜß und Blank
//                paranoid (4) =  a..z, A..Z, kein Blank
//   numeric   =  Parameter für numerische Werte (true = zulässig, false = unzulässig)
function chkString(strfield,level,numeric) {
// Merker: Diese Chars machen bei manchen MAC-Browsern Probleme
// Noch nicht mit dem Problem näher beschäftigt
var macchars = new Array(166,178,179,185,188,189,190,208,215,221,222,240,253,254)
// Den Index für die Fehleriterationsroutine ermitteln
  k = getOldvalindex(strfield)
// den Feldinhalt holen
  pVal = strfield.value
// Wenn das Feld leer ist, Funktion abbrechen
  if ( pVal == "" ) {
// Falls das Feld leer ist, aber vorher nicht leer war, den Fehleriterationscounter
// zurücksetzen
    if ((!(v_oldval[k] == pVal)) && (!(v_oldval[k] == ""))) erriteration=0
    return false
  }
// Den aktuellen Feldinhalt merken
  v_oldval[k] = pVal
  plevel = ""
// übergebene Stufe holen und umsetzen
  switch(level.toLowerCase()) {
    case "none":
      plevel=0;
      break;
    case "poor":
      plevel=1;
      break;
    case "normal":
      plevel=2;
      break;
    case "strict":
      plevel=3;
      break;
    case "paranoid":
      plevel=4;
      break;
    default:
      dispMsg(errmsg[lang][13] + level + ")")
      return false;
    break;
  }
  isOk = true
  i=0
// Die einzelnen Zeichen prüfen, beim ersten fehlerhaften Zeichen abbrechen und
// detaillierte Fehlermeldung ausgeben
  while ((i < pVal.length) && (isOk)) {
// Zu prüfendes Zeichen in latin-1-ISO-8859 umwandeln
    cC = pVal.charCodeAt(i)
// Die Steuerzeichen mit code-number < 32 prinzipiell ausschließen, unabhängig vom Prüflevel!
    if ( (cC < 32) ||
// Blanks nur bei Prüflevel 4 bemängeln
         ((cC == 32) && (plevel > 3)) ||
         ((cC > 32) && (cC < 45) && (plevel > 1)) ||
// '-' nur bei Prüflevel > 2 bemängeln
         ((cC == 45) && (plevel > 2)) ||
         ((cC > 45) && (cC < 48) && (plevel > 1)) ||
// [0..9] ist nur zulässig, wenn der Parameter numeric auf true steht
         ((cC >=48) && (cC <= 57) && (!numeric)) ||
         ((cC > 57) && (cC < 65) && (plevel > 1)) ||
// von code 65 - 90 stehen die stets zulässigen Großbuchstaben [A..Z]
         ((cC > 90) && (cC < 97) && (plevel > 1)) ||
// von code 97 - 122 stehen die stets zulässigen Kleinbuchstaben [a..z]
         ((cC > 122) && (cC < 127) && (plevel > 1)) ||
// Steuerzeichen (control codes) von nr. 127 bis 159 prinzipiell immer ausschließen
         ((cC >= 127) && (cC <= 159)) ||
         ((cC > 159) && (cC < 196) && (plevel > 1)) ||
// 'Ä' nur bei Prüflevel > 3 bemängeln
         ((cC == 196) && (plevel > 3)) ||
         ((cC > 196) && (cC < 214) && (plevel > 1)) ||
// 'Ö' nur bei Prüflevel > 3 bemängeln
         ((cC == 214) && (plevel > 3)) ||
         ((cC > 214) && (cC < 220) && (plevel > 1)) ||
// 'Ü' nur bei Prüflevel > 3 bemängeln
         ((cC == 220) && (plevel > 3)) ||
         ((cC > 220) && (cC < 223) && (plevel > 1)) ||
// 'ß' nur bei Prüflevel > 3 bemängeln
         ((cC == 223) && (plevel > 3)) ||
         ((cC > 223) && (cC < 228) && (plevel > 1)) ||
// 'ä' nur bei Prüflevel > 3 bemängeln
         ((cC == 228) && (plevel > 3)) ||
         ((cC > 228) && (cC < 246) && (plevel > 1)) ||
// 'ö' nur bei Prüflevel > 3 bemängeln
         ((cC == 246) && (plevel > 3)) ||
         ((cC > 246) && (cC < 252) && (plevel > 1)) ||
// 'ü' nur bei Prüflevel > 3 bemängeln
         ((cC == 252) && (plevel > 3)) ||
         ((cC > 252) && (plevel > 1)) ) isOk = false
    i++
  }
  if (!isOk) {
// Detaillierte Fehlermeldung mit Offset ausgeben, ...
     handleErr(strfield,errmsg[lang][10] + pVal.charAt(i-1) + errmsg[lang][11] + (i))
     return false
  }
  else {
// Wenn alles glatt geht, wird die evtl. vorhandene letzte Fehlermeldung in der
// Browserstatuszeile des Konqueror gelöscht
    window.status = ""
    return true
  }
}

// Überprüfung eines nummerischen Feldinhaltes, ob nummerisch, ggf. ob Wert > 0
// und führende "0" werden abgeschnitten
// Parameter:
//   numfield  =  Name des zu prüfenden Formularfeldes (z.B. "thisform.age") oder "this"
//   nullable  =  flag, ob das Feld auch den Wert 0 enthalten darf, Werte "true", "false"
function chkNum(numfield,nullable) {
// Den Index für die Fehleriterationsroutine ermitteln
  k = getOldvalindex(numfield)
//  alert("Index des Feldes " + numfield.name + ": " + k)
  passedVal = numfield.value
// Abbruch, falls Feldinhalt leer ist
//  alert("Übergebener Wert: " + passedVal + "\nGespeicherter Wert: " + v_oldval[k])
  if (passedVal == "") {
// Wenn das Feld leer ist aber vorher nicht leer war, den Fehleriterationscounter resetten
    if ((!(v_oldval[k] == passedVal)) && (!(v_oldval[k] == ""))) erriteration=0
    return true
  }
// Den aktuellen Feldinhalt merken
  v_oldval[k] = passedVal
  isNum = true
  i = 0
// Jedes einzelne Zeichen im Feldstring prüfen, ...
  while ((i<passedVal.length) && (isNum)) {
// ... falls Zeichen außerhalb der Range ['0', .. ,'9'], dann Flag isNum auf 'false' setzen
    if (passedVal.charAt(i) < "0") isNum = false
    if (passedVal.charAt(i) > "9") isNum = false
    i++
  }
  if (!isNum) {
// Wenn Flag 'false' ist, dann Fehlermeldung ausgeben, ...
    handleErr(numfield,errmsg[lang][0])
    return false
  }
// Wenn Feldinhalt nummerisch ist, dann führende Nullen mit truncNum abschneiden
  else passedVal = truncNum(passedVal.toString())
  numfield.value = passedVal
// Prüfung, ob Feldinhalt = '0' ist, falls '0' kein zulässiger Wert
  if ((!nullable) && (passedVal == 0)) {
// Fehlermeldung ausgeben, wenn Wert = '0', Feld fokussieren und Inhalt selektieren
    handleErr(numfield,errmsg[lang][1])
    return false
  }
  else {
//    alert("chkNum im OK-Zweig.")
// Evtl. vorhandene Fehlermeldung in der Browserstatuszeile des Konquerors entfernen
    window.status = ""
    return true
  }
}

// Überprüfung eines nummerischen Feldinhaltes, ob nummerisch, und ob Wert
// in der Range minValue und maxValue; führende "0" werden abgeschnitten
// Parameter:
//   chkfield  =  Name des zu prüfenden Formularfeldes (z.B. "thisform.age") oder "this"
//   minval    =  minimaler erlaubter Wert (Untergrenze)
//   maxval    =  maximaler erlaubter Wert (Obergrenze)
function chkNumval(chkfield,minval,maxval) {
// Den Index für die Fehleriterationsroutine ermitteln
  k = getOldvalindex(chkfield)
// Erst mal prüfen, ob die übergebenen Parameter korrekt sind
// maxval muss größer sein als minval, ...
  if (!(maxval > minval)) {
// ... falls nicht, dann Fehlermeldung ausgeben und Funktion abbrechen
    dispMsg(errmsg[lang][2])
    return false
  }
  else {
    passedVal = chkfield.value
// Falls der Feldinhalt nummerisch und das Feld nicht leer ist, ...
    if (chkNum(chkfield,true) && (passedVal != "")) {
// ... prüfen ob der Wert innerhalb der Range [minval, .., maxval] ist, ...
      if (!((passedVal >= minval) && (passedVal <= maxval))) {
// ... falls nicht, dann Fehlermeldung ausgeben, Feld fokussieren und Inhalt selektieren
        handleErr(chkfield,errmsg[lang][12] + minval + " - " + maxval + ")!")
        return false
      }
      else {
        window.status=""
        erriteration = 0
        return true
      }
// Falls Feldinhalt nicht nummerisch ist oder leer ist, dann greift entweder die
// Fehlermeldung der Funktion chkNum oder es passiert nichts
    }
    else {
// Falls das Feld leer ist, jedoch nicht leer war, Fehleriterationscounter resetten
      if ((passedVal == "") && (!(v_oldval[k] == passedVal)) && (!(v_oldval[k] == ""))) erriteration=0
      return true
    }
// Den aktuellen Feldinhalt merken
    v_oldval[k] = passedVal
  }
}

// Feldinhalt in Groß-/Kleinbuchstaben umwandeln
// Parameter:
//   charfield  =  Name des zu behandelnden Formularfeldes (z.B. "thisform.lastname") oder "this"
//   capitalize =  Umwandlungstyp (true = alles groß; false = alles klein)
//   strchk     =  String auf ungültige (Sonder-)Zeichen prüfen (true; false)
//   strlevel   =  Prüfstufe für Funktion chkString() siehe function
//   strnum     =  numeric-Flag für Funktion chkString()
function setAllcapchar(charfield,capitalize,strchk,strlevel,strnum) {
// Den Index für die Fehleriterationsroutine ermitteln
  k = getOldvalindex(charfield)
  passedVal = charfield.value;
// Falls das zu prüfende Feld leer ist, dann bleibt Funktion wirkungslos
  if (!(passedVal == "")) {
// Falls der Parameter capitalize auf 'true' steht, dann den String in Großbuchstaben
// konvertieren, ...
    if (capitalize) charfield.value = passedVal.toUpperCase()
// ... falls der capitalize = 'false', dann den String in Kleinbuchstaben umwandeln
    else charfield.value = passedVal.toLowerCase()
    if (strchk) chkString(charfield,strlevel,strnum)
//    else erriteration=0
  }
  else {
// Falls das Feld leer ist, jedoch vorher nicht leer war, Fehleriterationscounter resetten
    if ((!(v_oldval[k] == passedVal)) && (!(v_oldval[k] == ""))) erriteration=0
    return true
  }
// Den aktuellen Feldinhalt merken
  v_oldval[k] = passedVal
}

// Ersten Buchstaben in Großbuchstaben umwandeln, Rest wird immer in Kleinbuchstaben transformiert
// Parameter:
//   charfield  =  Name des zu behandelnden Formularfeldes (z.B. "thisform.lastname") oder "this"
//   strchk     =  String auf ungültige (Sonder-)Zeichen prüfen (true; false)
//   strlevel   =  Prüfstufe für Funktion chkString() siehe function
//   strnum     =  numeric-Flag für Funktion chkString()
function setFirstcapchar(charfield,strchk,strlevel,strnum) {
// Den Index für die Fehleriterationsroutine ermitteln
  k = getOldvalindex(charfield)
// Erst mal den String in Kleinbuchstaben umwandeln
  passedVal = charfield.value.toLowerCase()
// Falls Feldinhalt leer ist, Funktion abbrechen
  if (!(passedVal == "")) {
// Nun den ersten Buchstaben des Strings in einen Großbuchstaben umwandeln
    charfield.value = passedVal.charAt(0).toUpperCase().concat(passedVal.substring(1,passedVal.length))
    if (strchk) { chkString(charfield,strlevel,strnum) }
//    else erriteration=0
  }
  else {
// Falls das Feld leer ist, jedoch vorher nicht leer war, Fehleriterationscounter resetten
    if ((!(v_oldval[k] == passedVal)) && (!(v_oldval[k] == ""))) erriteration=0
    return true
  }
// Den aktuellen Feldinhalt merken
  v_oldval[k] = passedVal
}

// Syntaktische Überprüfung eines eMail-Feldes und Transformation in Kleinbuchstaben
// Parameter:
//   mailadrfield  =  name des Formularfeldes (z.B. "thisform.email") oder "this"
function chkMailaddress(mailadrfield) {
// Den Index für die Fehleriterationsroutine ermitteln
  k = getOldvalindex(mailadrfield)
// Erst mal die eMail-Adresse in Kleinbuchstaben umwandeln
  email = mailadrfield.value.toLowerCase()
  mailadrfield.value = email
  isOk = true
// Falls das Feld leer ist, Funktion abbrechen
  if (email == "") {
// Falls das Feld vor dem letzten Aufruf nicht leer war, den Iterationscounter zurücksetzen
    if ((!(v_oldval[k] == email)) && (!(v_oldval[k] == ""))) erriteration=0
    return true
  }
// Den aktuellen Feldinhalt merken
  v_oldval[k] = email
  for (i=0; i < email.length; i++) {
    cC = email.charCodeAt(i)
// es werden nur die Zeichen a-z, 0-9 sowie ".", "_", "@" und  "-" akzeptiert
    if ((cC < 45) || (cC == 47) || ((cC > 57)&& (cC < 64)) ||
       ((cC > 64) && (cC < 95)) || (cC == 96) || (cC > 123)) isOk = false
  }
// Der String muss ein '@' enthalten
  atPos = email.indexOf("@",1)
  if (atPos == -1) isOk = false
// Es dürfen keine zwei '@' aufeinander folgen
  else if (email.indexOf("@",atPos+1) > -1) isOk = false
  periodPos = email.indexOf(".",atPos)
// Ähnliches gilt auch für einen '.', einer muss da sein ...
  if (periodPos == -1) isOk = false
// ... und danach müssen auch noch mindestens 2 weitere Zeichen folgen (z.B. "de")
  else if (periodPos+3 > email.length) isOk = false
// Keine zwei '.' dürfen aufeinander folgen
  if (email.indexOf("..",1) > -1) isOk = false
// Im Fehlerfall wird eine Fehlermeldung angezeigt, das Feld fokussiert und der
// Inhalt selektiert
  if (!isOk) {
   handleErr(mailadrfield,errmsg[lang][3])
   return false
  }
  else {
// Wenn alles glatt geht, wird die evtl. vorhandene letzte Fehlermeldung in der
// Browserstatuszeile des Konqueror gelöscht
    window.status = ""
    return true
  }
// ToDo:
//   Diese Funktion leistet zunächst nur eine relativ einfache syntaktische Prüfung eines eMail-Adress-Strings
//   Die Funktion liese sich noch verfeinern, aber ob sich das wirklich rentiert sei mal dahingestellt
//   Ich plane zunächst keine Weiterentwicklung des Prüfmechanismus
}

// Überprüfung eines Datumsfeldes
// Parameter:
//   datefield  =  Name des Formularfeldes (z.B. "thisform.birthday") oder "this"
//   allowtime  =  der zu prüfende timestamp darf eine Uhrzeit enthalten
//                 time_purge  =  Uhrzeit nicht zulässig, wird abgeschnitten
//                 time_allow  =  Uhrzeit optional zulässig
//                 time_force  =  Uhrzeit zwingend vorgeschrieben
//   warnrange  =  Soll Jahr geprüft werden (year < current_year; year > current_year+1)
//                 (true = ja; false = nein)
//   mindate    =  kleinstes zulässig Datum [optional]
//                 'now'  =  heutiges Datum (Datum darf nicht in der Vergangenheit liefen)
//                 ansonsten ein value in der Form TT.MM.JJJJ, idealerweise ein Wert, der
//                 bereits einmal durch die Funktion chkDate() gelaufen ist
function chkDate(datefield,allowtime,warnrange,mindate) {
// Den Index für die Fehleriterationsroutine ermitteln
  k = getOldvalindex(datefield)
// Das sind die gültigen Zeichen in einem Datums-/Timestring
  validChars="0123456789.: "
// Feldinhalt holen
  datum = datefield.value
// Wenn Feldinhalt leer ist, Funktion abbrechen
  if (datum == "") {
// Falls das Feld leer ist, jedoch vorher nicht leer war, Fehleriterationscounter resetten
    if ((!(v_oldval[k] == datum)) && (!(v_oldval[k] == ""))) erriteration=0
    return ""
  }
// Den aktuellen Feldinhalt merken
  v_oldval[k] = datum
// übergebene Stufe holen und umsetzen
  switch(allowtime.toLowerCase()) {
    case "time_purge":
      plevel=0;
      break;
    case "time_allow":
      plevel=1;
      break;
    case "time_force":
      plevel=2;
      break;
    default:
      dispMsg(errmsg[lang][13] + allowtime + ")")
      return false;
      break;
  }
// Anhand des aktuellen Systemdatums ...
  jetzt = new Date()
// ... das aktuelle Jahr, Monat und Tag ermitteln
  curryear = jetzt.getFullYear()
  currmonth = jetzt.getMonth()
  currday = jetzt.getDate()
  alertmsg=""
  firstfieldok=1
  i = 0
// Solange kein fehlerhaftes Zeichen gefunden wurde, den String Zeichenweise prüfen ...
  while ((i < datum.length) && (alertmsg == "")) {
    testChar = datum.charAt(i)
// ... falls ein ungültiges Zeichen enthalten ist, Fehlermeldung ausgeben und weitere
// Prüfungen abbrechen
    if (validChars.indexOf(testChar,0) == -1) {
      firstfieldok = 0
      alertmsg=errmsg[lang][10] + datum.charAt(i) + errmsg[lang][11] + (i+1)
    }
    i++
  }
// Wenn eine Fehlermeldung generiert wurde, ausgeben und Funktion wie üblich beenden
  if (alertmsg != "") {
   handleErr(datefield,alertmsg)
   return false
  }
  else {
// Wie immer evtl. alte Konqueror-Fehlermeldung clearen
    window.status = ""
// Hier beginnt die logische Prüfung des Datumsteils
// Zur einfacheren Prüfung, Feldinhalt normalisieren und ':' durch '.' ersetzen
    tmpdate = datum.replace(/:/,".")
// String in Teilstring splitten, die durch ' ' getrennt sind
    var tmpdatesplit = tmpdate.split(" ")
// Ersten Teilstring ebenfalls aufsplitten. Trennzeichen ist '.'
    var tmpdatesplit_date = tmpdatesplit[0].split(".")
    i = 0
// Jetzt für alle Teilstrings des Datums ...
    while (tmpdatesplit_date[i]) {
// ... erst mal führende '0' eliminieren, und dann weitere Prüfungen durchführen
      tmpdatesplit_date[i] = truncNum(tmpdatesplit_date[i].toString());
      if (i==0) {
// Der Wert für Tag TT muss zwischen 1 und 31 liegen (weitere Prüfungen, abhängig vom Monat) werden
// nicht durchgeführt
        if (!((tmpdatesplit_date[i] > 0) && (tmpdatesplit_date[i] <= 31))) {
// Wenn Wert außerhalb des gültigen Wertebereichs liegt, Fehlermeldung ausgeben und Funktion wie üblich abbrechen
          handleErr(datefield,errmsg[lang][4])
          return false
        }
// Wenn der Wert OK ist, wieder mit einer führenden '0' versehen, falls der Wert < 10 ist
        else if (tmpdatesplit_date[i].length < 2) tmpdatesplit_date[i]="0" + tmpdatesplit_date[i]
      }
      if (i==1) {
// Analoge Prüfung auch für den Monatswert MM (gültiger Wertebereich von 1 - 12
        if (!((tmpdatesplit_date[i] > 0) && (tmpdatesplit_date[i] <= 12))) {
          handleErr(datefield,errmsg[lang][5])
          return false
        }
        else if (tmpdatesplit_date[i].length < 2) tmpdatesplit_date[i]="0" + tmpdatesplit_date[i]
      }
      if (i==2) {
// Der Wert für Jahr wird erst mal auf 4 Stellen normalisiert
// bei 2-stelliger Jahresangabe wird eine '19' oder '20' vorangestellt
        if (tmpdatesplit_date[i].length < 2) tmpdatesplit_date[i]= "0" + tmpdatesplit_date[i]
        if (tmpdatesplit_date[i].length < 4) {
          chkdateyear=Number(tmpdatesplit_date[i].toString())
          maxval=curryear+5
          if ((chkdateyear < Number(curryear.toString().substring(2,4))) ||
              (chkdateyear > Number(maxval.toString().substring(2,4))))
            tmpdatesplit_date[i]= "19" + tmpdatesplit_date[i]
          else tmpdatesplit_date[i]= "20" + tmpdatesplit_date[i]
        }
      }
      i++
// Falls der Datumsstring nur 1 Bestandteil (also TT) hat, dann Fehlermeldung ...
      if ((i==1) && (!tmpdatesplit_date[i])) {
        handleErr(datefield,errmsg[lang][6])
        return false
      }
// ... falls TT und MM vorhanden sind, wird JJJJ mit dem aktuellen Jahr belegt
      if ((i==2) && (!tmpdatesplit_date[i])) tmpdatesplit_date[i]=curryear
    }
// Jetzt wird das Datum aus den einzelnen geprüften und z.T. normalisierten Bestandteilen wieder
// zusammengefügt und in der Variable newdate gespeichert
    newdate=""
    for (i=0; i<2; i++) newdate=newdate + tmpdatesplit_date[i] + "."
    newyear = tmpdatesplit_date[2]
    newdate=newdate + newyear
// Falls Uhrzeit gefordert aber nicht vorhanden, dann Fehlermeldung ausgeben
   if ((!tmpdatesplit[1]) && (plevel == 2)) {
     handleErr(datefield,errmsg[lang][15])
     return false
   }
// Hier beginnt die Überprüfung der evtl. vorhandenen Uhrzeitangabe
    if ((tmpdatesplit[1]) && (plevel > 0)) {
      newtime=""
// Eine analoge Aufsplittung der Uhrzeit vornehmen
      var tmpdatesplit_time = tmpdatesplit[1].split(".")
      i=0
// Und auch wieder die Wertebereichsüberprüfung für die einzelnen Teilstrings HH und MM durchführen
      while (tmpdatesplit_time[i]) {
        tmpdatesplit_time[i] = truncNum(tmpdatesplit_time[i].toString())
        if (i==0) {
// Gültiger Wertebereich für Stunden HH ist 0 - 24
          if (!((tmpdatesplit_time[i] >= 0) && (tmpdatesplit_time[i] <= 24))) {
// Je nach Prüfungsergebnis ggf. Fehlermeldung erzeugen und Funktion wie üblich abbrechen
            handleErr(datefield,errmsg[lang][7])
            return false
          }
          else if (tmpdatesplit_time[i].length < 2) tmpdatesplit_time[i]="0" + tmpdatesplit_time[i]
        }
        if (i==1) {
// Gültiger Wertebereich für Minuten MM ist 0 - 59
          if (!((tmpdatesplit_time[i] >= 0) && (tmpdatesplit_time[i] <= 59))) {
// Je nach Prüfungsergebnis ggf. Fehlermeldung erzeugen und Funktion wie üblich abbrechen
            handleErr(datefield,errmsg[lang][8])
            return false
          }
          else if (tmpdatesplit_time[i].length < 2) tmpdatesplit_time[i]="0" + tmpdatesplit_time[i]
        }
        i++
// Wenn keine Minuten MM angegeben wurden, wird MM mit '00' belegt
        if ((i==1) && (!tmpdatesplit_time[i])) tmpdatesplit_time[i]="00"
      }
// Zum Schluß wieder die geprüfte und normalisierte Uhrzeit zusammenbauen und in newtime speichern
      newtime = tmpdatesplit_time[0] + ":" + tmpdatesplit_time[1]
// Das Datum um die Uhrzeit ergänzen
      newdate = newdate + " " + newtime
    }
// Das Timestamp-Feld mit dem geprüften und normalisierten Wert überschreiben
    datefield.value = newdate
    mindef = (typeof(mindate) != 'undefined')
// Hier prüfen, ob das Ende-Datum nach dem Anfangsdatum (inkl. Uhrzeit) liegt
    if (mindef == true) {
      if (debug) alert(mindate)
      if ((mindate == "now") || (mindate == "today")) {
        chkdate_begin = Date.UTC(curryear,currmonth,currday) -1
        if (debug) alert(chkdate_begin)
        isnow = true
      }
      else {
        chkdate_begin = Date.UTC(Number(mindate.substring(6,10)),Number(mindate.substring(3,5))-1,
                        Number(mindate.substring(0,2)),Number(mindate.substring(11,13)),
                        Number(mindate.substring(14,16)))
        isnow = false
      }
      chkdate_end = Date.UTC(Number(newdate.substring(6,10)),Number(newdate.substring(3,5))-1,
                    Number(newdate.substring(0,2)),Number(newdate.substring(11,13)),
                    Number(newdate.substring(14,16)))
      if (debug) alert("Startdatum: " + chkdate_begin + "\nEndedatum: " + chkdate_end)
      if (chkdate_end <= chkdate_begin) {
        if (isnow) handleErr(datefield,errmsg[lang][18])
        else handleErr(datefield,errmsg[lang][17])
        return false
      }
    }
// Falls die Jahresangabe kleiner ist als das aktuelle Jahr oder mehr als ein Jahr in die
// Zukunft zeigt eine Hinweismeldung ausgeben.
// Das ist keine Fehlermeldung und führt auch zu keinem Abbruch, nur ein Hinweis!
    if (warnrange) {
      if ((newyear < curryear) || (newyear > curryear+1))
        dispMsg(errmsg[lang][9])
    }
    else {
// Hmm, gute Frage, aber irgendwas sollte hier noch passieren, glaub ich.
    }
  }
  return true
}

// Setzt Enddatum beim ersten Editieren gleich dem Anfangsdatum
//(Uhrzeit wird nur optional übernommen)
// Parameter:
//   begindatefield  =  name des Formularfeldes, das den zu übernehmenden Wert enthält
//                      (z.B. "thisform.date_begin")
//   setdatefield    =  Name des Formularfeldes, in das der Wert übernommen werden soll
//                      (z.B. "thisform.date_end") oder "this"
//   allowtime       =  der zu übernehmende timestamp darf eine Uhrzeit enthalten
//                      (Stufen siehe Funktion chkDate)
//   warnrange       =  Soll Jahr geprüft werden (year < current_year; year > current_year+1)
//                      (true = ja; false = nein)
//   mindate         =  kleinstes zulässiges Datum
// Die Parameter allowtime, warnrange und mindate müssen der Funktion chkDate() übergeben werden
function getBegindate(begindatefield,setdatefield,allowtime,warnrange,mindate) {
// Falls Datumsprüfung des zu übernehmenden Datumsstrings OK ist, ...
  if (chkDate(begindatefield,allowtime,warnrange,mindate)) {
// ... und es sich um den erstmaligen Aufruf dieser Funktion handelt, ...
    if (firstiteration == 0) {
// ... und das aktuelle Datumsfeld den Wert noch nicht enthält, ...
      if (setdatefield.value != begindatefield.value) {
// ... dann den Datumswert übernehmen, aber nur das Datum, ...
        tmpbeginfield = begindatefield.value.split(" ")
// ... eine evtl. vorhandene Zeitangabe wird abgeschnitten
        setdatefield.value = tmpbeginfield[0] + " "
// Merken, dass die Funktion durchgeführt wurde
// Sie wird damit kein zweites Mal durchgeführt, außer es findet ein Page-Reload statt
// oder das Formular wird zurückgesetzt (siehe Funktion doFormreset)
        firstiteration=1
      }
    }
  }
  return true
}

//
// Zentrale ToDo's:
//   - Der Fehleriterationscounter arbeitet derzeit noch nicht zuverlässig bei Funktionsübergreifender
//     Nutzung, d.h. insbesondere bei der Verschachtelung einzelner Funktionen. Das Problem muss noch
//     genauer analysiert werden. Ggf. muss der derzeit zentrale Iterationscounter in ein Array umgebaut
//     werden um pro Funktion einen eigenen Counter führen zu können.
//
// Vielen Dank für die Aufmerksamkeit
// Falls Sie noch Fragen haben, einfach SelfHTML studieren oder andere gute JavaScript-Lektüre wälzen
// (z.B. 'JavaScript fürs World Wide Web (Tom Negrino & Dori Smith)', Markt+Technik)
// Weitere Inspirationen gibt es auf einschlägigen Webseiten.
//
// Ende der jslib-forms.js-->
