/* ------------------------------------------------------
-plaid account(sandbox)
------------------------------------------------------- */
var plaid_client_id="58604130393619270a2ec528";
var plaid_secret="ada3419e7b3888f94e05b782c9567a";


var member_info = $.cookie("memberInfo");
try {
    member_info=$.parseJSON(member_info);
}catch (e){
    console.log('invalid member info: ');
}
console.log("member_info:");
console.log(member_info);

var token = $.cookie("token");
console.log("token: "+token);
var secret_passphrase="123456";

var BASE_URL="https://dev.scrivon.com";
var CHAT_CONNECT_URL=BASE_URL+":2195";


