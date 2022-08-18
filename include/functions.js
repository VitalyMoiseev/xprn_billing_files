function openuser(userid){
    var url1 = "/биллинг/user/" + userid;
    NewWin1 = window.open(url1,'w1','width=700,height=650,location=no,toolbar=no,menubar=no,status=no,scrollbars=yes,resizable=yes');
}
function openuserwork(userid){
    var url1 = "/биллинг/userwork/" + userid;
    NewWin2 = window.open(url1,'w2','width=1300,height=600,location=no,toolbar=no,menubar=no,status=no,scrollbars=yes,resizable=yes');
}
function openmoney(moneyid){
    var url1 = "/биллинг/money/" + moneyid;
    NewWin8 = window.open(url1,'w8','width=460,height=500,location=no,toolbar=no,menubar=no,status=no,scrollbars=yes,resizable=yes');
}
