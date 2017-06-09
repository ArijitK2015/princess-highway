Validation.add('validate-future-date','Available date must be in the future!',function(v){
    if (v) {
        var toValidate = v.split("/");
        var test = new Date(toValidate[2],toValidate[1]-1,toValidate[0]);
        var now = new Date();
        return test.getTime() > now.getTime();
    } else {
        return true;
    }
});