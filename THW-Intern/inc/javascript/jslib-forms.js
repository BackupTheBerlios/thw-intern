<!-- jslib-forms.js  Version 1.6beta1
var firstiteration=0
var erriteration=0
var maxerr=3
var i_oldval = new Array()
var v_oldval = new Array()
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
lang = lang_de
function getOldvalindex(ofield) {
  elname = ofield.name
  j=0
  while ((j < i_oldval.length) && (!(elname == i_oldval[j]))) {
    j++
  }
  if (j == i_oldval.length) {
    i_oldval.push(elname)
    v_oldval.push("")
  }
  return j
}
function setLanguage(lng) {
  switch(lng.toString().toLowerCase()) {
    case "0":
      lang = lang_de;
      break;
    case "1":
      lang = lang_en;
      break;
    default:
      handleErr(errmsg[lang][14] + lng + ")")
      return false;
    break;
  }
  return true
}
function dispMsg(message) {
  if (navigator.appName == "Konqueror") window.status = message
  else alert(message)
  return true
}
function handleErr(formelement,msgerr) {
  if (erriteration < maxerr) {
    erriteration++
    dispMsg(msgerr)
    formelement.select()
    formelement.focus()
  }
  else {
    erriteration=0
    formelement.value = ""
    window.status = ""
  }
  return true
}
function showRterror(rterrmsg,file,line) {
   rtmessage = errmsg[lang][16] + "\n"+ rterrmsg + "\n" + file + "\nSource line: " + line + "\n" + errmsg[lang][19]
   alert(rtmessage)
   return true
}
function setFirstfocus(whichform) {
  l = 0
  chktypeerror = ((whichform.elements[l].type == "undefined") || (whichform.elements[l].type == "hidden"))
  while ((chktypeerror == true) && (whichform.elements[l])) {
   	l++
    chktypeerror = ((whichform.elements[l].type == "undefined") || (whichform.elements[l].type == "hidden"))
  }
  if (whichform.elements[l]) {
    whichform.elements[l].focus()
    whichform.elements[l].select()
    return true
  }
  return false
}
function doFormreset(whichform) {
  firstiteration=0
  erriteration=0
  setFirstfocus(whichform)
  return true
}
function truncNum(val) {
  var pVal = val
  while ((pVal.charAt(0) == "0") && (pVal.length > 1)) pVal = pVal.substring(1,pVal.length)
  return pVal
}
function chkString(strfield,level,numeric) {
var macchars = new Array(166,178,179,185,188,189,190,208,215,221,222,240,253,254)
  k = getOldvalindex(strfield)
  pVal = strfield.value
  if ( pVal == "" ) {
    if ((!(v_oldval[k] == pVal)) && (!(v_oldval[k] == ""))) erriteration=0
    return false
  }
  v_oldval[k] = pVal
  plevel = ""
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
  while ((i < pVal.length) && (isOk)) {
    cC = pVal.charCodeAt(i)
    if ( (cC < 32) ||
         ((cC == 32) && (plevel > 3)) ||
         ((cC > 32) && (cC < 45) && (plevel > 1)) ||
         ((cC == 45) && (plevel > 2)) ||
         ((cC > 45) && (cC < 48) && (plevel > 1)) ||
         ((cC >=48) && (cC <= 57) && (!numeric)) ||
         ((cC > 57) && (cC < 65) && (plevel > 1)) ||
         ((cC > 90) && (cC < 97) && (plevel > 1)) ||
         ((cC > 122) && (cC < 127) && (plevel > 1)) ||
         ((cC >= 127) && (cC <= 159)) ||
         ((cC > 159) && (cC < 196) && (plevel > 1)) ||
         ((cC == 196) && (plevel > 3)) ||
         ((cC > 196) && (cC < 214) && (plevel > 1)) ||
         ((cC == 214) && (plevel > 3)) ||
         ((cC > 214) && (cC < 220) && (plevel > 1)) ||
         ((cC == 220) && (plevel > 3)) ||
         ((cC > 220) && (cC < 223) && (plevel > 1)) ||
         ((cC == 223) && (plevel > 3)) ||
         ((cC > 223) && (cC < 228) && (plevel > 1)) ||
         ((cC == 228) && (plevel > 3)) ||
         ((cC > 228) && (cC < 246) && (plevel > 1)) ||
         ((cC == 246) && (plevel > 3)) ||
         ((cC > 246) && (cC < 252) && (plevel > 1)) ||
         ((cC == 252) && (plevel > 3)) ||
         ((cC > 252) && (plevel > 1)) ) isOk = false
    i++
  }
  if (!isOk) {
     handleErr(strfield,errmsg[lang][10] + pVal.charAt(i-1) + errmsg[lang][11] + (i))
     return false
  }
  else {
    window.status = ""
    return true
  }
}
function chkNum(numfield,nullable) {
  k = getOldvalindex(numfield)
  passedVal = numfield.value
  if (passedVal == "") {
    if ((!(v_oldval[k] == passedVal)) && (!(v_oldval[k] == ""))) erriteration=0
    return true
  }
  v_oldval[k] = passedVal
  isNum = true
  i = 0
  while ((i<passedVal.length) && (isNum)) {
    if (passedVal.charAt(i) < "0") isNum = false
    if (passedVal.charAt(i) > "9") isNum = false
    i++
  }
  if (!isNum) {
    handleErr(numfield,errmsg[lang][0])
    return false
  }
  else passedVal = truncNum(passedVal.toString())
  numfield.value = passedVal
  if ((!nullable) && (passedVal == 0)) {
    handleErr(numfield,errmsg[lang][1])
    return false
  }
  else {
    window.status = ""
    return true
  }
}
function chkNumval(chkfield,minval,maxval) {
  k = getOldvalindex(chkfield)
  if (!(maxval > minval)) {
    dispMsg(errmsg[lang][2])
    return false
  }
  else {
    passedVal = chkfield.value
    if (chkNum(chkfield,true) && (passedVal != "")) {
      if (!((passedVal >= minval) && (passedVal <= maxval))) {
        handleErr(chkfield,errmsg[lang][12] + minval + " - " + maxval + ")!")
        return false
      }
      else {
        window.status=""
        erriteration = 0
        return true
      }
    }
    else {
      if ((passedVal == "") && (!(v_oldval[k] == passedVal)) && (!(v_oldval[k] == ""))) erriteration=0
      return true
    }
    v_oldval[k] = passedVal
  }
}
function setAllcapchar(charfield,capitalize,strchk,strlevel,strnum) {
  k = getOldvalindex(charfield)
  passedVal = charfield.value;
  if (!(passedVal == "")) {
    if (capitalize) charfield.value = passedVal.toUpperCase()
    else charfield.value = passedVal.toLowerCase()
    if (strchk) chkString(charfield,strlevel,strnum)
  }
  else {
    if ((!(v_oldval[k] == passedVal)) && (!(v_oldval[k] == ""))) erriteration=0
    return true
  }
  v_oldval[k] = passedVal
}
function setFirstcapchar(charfield,strchk,strlevel,strnum) {
  k = getOldvalindex(charfield)
  passedVal = charfield.value.toLowerCase()
  if (!(passedVal == "")) {
    charfield.value = passedVal.charAt(0).toUpperCase().concat(passedVal.substring(1,passedVal.length))
    if (strchk) { chkString(charfield,strlevel,strnum) }
  }
  else {
    if ((!(v_oldval[k] == passedVal)) && (!(v_oldval[k] == ""))) erriteration=0
    return true
  }
  v_oldval[k] = passedVal
}
function chkMailaddress(mailadrfield) {
  k = getOldvalindex(mailadrfield)
  email = mailadrfield.value.toLowerCase()
  mailadrfield.value = email
  isOk = true
  if (email == "") {
    if ((!(v_oldval[k] == email)) && (!(v_oldval[k] == ""))) erriteration=0
    return true
  }
  v_oldval[k] = email
  for (i=0; i < email.length; i++) {
    cC = email.charCodeAt(i)
    if ((cC < 45) || (cC == 47) || ((cC > 57)&& (cC < 64)) ||
       ((cC > 64) && (cC < 95)) || (cC == 96) || (cC > 123)) isOk = false
  }
  atPos = email.indexOf("@",1)
  if (atPos == -1) isOk = false
  else if (email.indexOf("@",atPos+1) > -1) isOk = false
  periodPos = email.indexOf(".",atPos)
  if (periodPos == -1) isOk = false
  else if (periodPos+3 > email.length) isOk = false
  if (email.indexOf("..",1) > -1) isOk = false
  if (!isOk) {
   handleErr(mailadrfield,errmsg[lang][3])
   return false
  }
  else {
    window.status = ""
    return true
  }
}
function chkDate(datefield,allowtime,warnrange,mindate) {
  k = getOldvalindex(datefield)
  validChars="0123456789.: "
  datum = datefield.value
  if (datum == "") {
    if ((!(v_oldval[k] == datum)) && (!(v_oldval[k] == ""))) erriteration=0
    return ""
  }
  v_oldval[k] = datum
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
  jetzt = new Date()
  curryear = jetzt.getFullYear()
  currmonth = jetzt.getMonth()
  currday = jetzt.getDate()
  alertmsg=""
  firstfieldok=1
  i = 0
  while ((i < datum.length) && (alertmsg == "")) {
    testChar = datum.charAt(i)
    if (validChars.indexOf(testChar,0) == -1) {
      firstfieldok = 0
      alertmsg=errmsg[lang][10] + datum.charAt(i) + errmsg[lang][11] + (i+1)
    }
    i++
  }
  if (alertmsg != "") {
   handleErr(datefield,alertmsg)
   return false
  }
  else {
    window.status = ""
    tmpdate = datum.replace(/:/,".")
    var tmpdatesplit = tmpdate.split(" ")
    var tmpdatesplit_date = tmpdatesplit[0].split(".")
    i = 0
    while (tmpdatesplit_date[i]) {
      tmpdatesplit_date[i] = truncNum(tmpdatesplit_date[i].toString());
      if (i==0) {
        if (!((tmpdatesplit_date[i] > 0) && (tmpdatesplit_date[i] <= 31))) {
          handleErr(datefield,errmsg[lang][4])
          return false
        }
        else if (tmpdatesplit_date[i].length < 2) tmpdatesplit_date[i]="0" + tmpdatesplit_date[i]
      }
      if (i==1) {
        if (!((tmpdatesplit_date[i] > 0) && (tmpdatesplit_date[i] <= 12))) {
          handleErr(datefield,errmsg[lang][5])
          return false
        }
        else if (tmpdatesplit_date[i].length < 2) tmpdatesplit_date[i]="0" + tmpdatesplit_date[i]
      }
      if (i==2) {
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
      if ((i==1) && (!tmpdatesplit_date[i])) {
        handleErr(datefield,errmsg[lang][6])
        return false
      }
      if ((i==2) && (!tmpdatesplit_date[i])) tmpdatesplit_date[i]=curryear
    }
    newdate=""
    for (i=0; i<2; i++) newdate=newdate + tmpdatesplit_date[i] + "."
    newyear = tmpdatesplit_date[2]
    newdate=newdate + newyear
   if ((!tmpdatesplit[1]) && (plevel == 2)) {
     handleErr(datefield,errmsg[lang][15])
     return false
   }
    if ((tmpdatesplit[1]) && (plevel > 0)) {
      newtime=""
      var tmpdatesplit_time = tmpdatesplit[1].split(".")
      i=0
      while (tmpdatesplit_time[i]) {
        tmpdatesplit_time[i] = truncNum(tmpdatesplit_time[i].toString())
        if (i==0) {
          if (!((tmpdatesplit_time[i] >= 0) && (tmpdatesplit_time[i] <= 24))) {
            handleErr(datefield,errmsg[lang][7])
            return false
          }
          else if (tmpdatesplit_time[i].length < 2) tmpdatesplit_time[i]="0" + tmpdatesplit_time[i]
        }
        if (i==1) {
          if (!((tmpdatesplit_time[i] >= 0) && (tmpdatesplit_time[i] <= 59))) {
            handleErr(datefield,errmsg[lang][8])
            return false
          }
          else if (tmpdatesplit_time[i].length < 2) tmpdatesplit_time[i]="0" + tmpdatesplit_time[i]
        }
        i++
        if ((i==1) && (!tmpdatesplit_time[i])) tmpdatesplit_time[i]="00"
      }
      newtime = tmpdatesplit_time[0] + ":" + tmpdatesplit_time[1]
      newdate = newdate + " " + newtime
    }
    datefield.value = newdate
    mindef = (typeof(mindate) != 'undefined')
    if (mindef == true) {
      if ((mindate == "now") || (mindate == "today")) {
        chkdate_begin = Date.UTC(curryear,currmonth,currday) -1
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
      if (chkdate_end <= chkdate_begin) {
        if (isnow) handleErr(datefield,errmsg[lang][18])
        else handleErr(datefield,errmsg[lang][17])
        return false
      }
    }
    if (warnrange) {
      if ((newyear < curryear) || (newyear > curryear+1))
        dispMsg(errmsg[lang][9])
    }
    else {
    }
  }
  return true
}
function getBegindate(begindatefield,setdatefield,allowtime,warnrange,mindate) {
  if (chkDate(begindatefield,allowtime,warnrange,mindate)) {
    if (firstiteration == 0) {
      if (setdatefield.value != begindatefield.value) {
        tmpbeginfield = begindatefield.value.split(" ")
        setdatefield.value = tmpbeginfield[0] + " "
        firstiteration=1
      }
    }
  }
  return true
}
// Ende der jslib-forms.js-->