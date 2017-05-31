function validateName(str, allowEmpty = false, extraCharacters = []) {
    if (str === "" && allowEmpty) {
        return true;
    }
    if (validateExtraCharacters(str, extraCharacters)) {
        return true;
    }
    var reg = /^\w{1,60}$/;
    return reg.test(str);
}

function validateSplitName(str, allowEmpty = false, extraCharacters = []) {
    if (str === "" && allowEmpty) {
        return true;
    }
    if (validateExtraCharacters(str, extraCharacters)) {
        return true;
    }
    var reg = /^\w{1,60}\s*,\s*\w{1,60}$/;
    return reg.test(str);
}

function validateNameWithSpaces(str, allowEmpty = false, extraCharacters = []) {
    if (str === "" && allowEmpty) {
        return true;
    }
    if (validateExtraCharacters(str, extraCharacters)) {
        return true;
    }
    var reg = /^[\w\s]{1,60}$/;
    return reg.test(str);
}

function validateNumber(str, allowEmpty = false, extraCharacters = []) {
    if (str === "" && allowEmpty) {
        return true;
    }
    if (validateExtraCharacters(str, extraCharacters)) {
        return true;
    }
    var reg = /^\d+$/;
    return reg.test(str);
}

function validateDate(str, allowEmpty = false, extraCharacters = []) {
    if (str === "" && allowEmpty) {
        return true;
    }
    if (validateExtraCharacters(str, extraCharacters)) {
        return true;
    }
    var reg = /^\d{4}-\d{2}-\d{2}$/;
    return reg.test(str);
}

function validateTime(str, allowEmpty = false, extraCharacters = []) {
    if (str === "" && allowEmpty) {
        return true;
    }
    if (validateExtraCharacters(str, extraCharacters)) {
        return true;
    }
    var reg = /^\d{2}:\d{2}$/;
    return reg.test(str);
}

function validateSelect(select, allowEmpty = false, extraCharacters = []) {
    if (!select) {
        return false;
    }
    return $(select).val() != "default";
}

function validateNameOrUsername(str, allowEmpty = false, extraCharacters = []) {
    if (str === "" && allowEmpty) {
        return true;
    }
    if (validateExtraCharacters(str, extraCharacters)) {
        return true;
    }
    if (str.charAt(0) === ':') {
        return validateName(str.substring(1));
    } else {
        return validateSplitName(str);
    }
}

function validateExtraCharacters(str, extraCharacters) {
    for (var i = 0; i < extraCharacters.length; i++) {
        if (str === extraCharacters[i]) {
            return true;
        }
    }
    return false;
}

function validatePassword(str, allowEmpty = false, extraCharacters = []) {
    if (str === "" && allowEmpty) {
        return true;
    }
    var reg = /^\S{1,60}$/;
    return reg.test(str);
}

function validateInputs(data, errorClass) {
    var validated = true;
    
    for(var i = 0; i < data.length; i++) {
        var element = data[i];
        var input = element.input;
        var validateFunc = element.validateFunc;
        var allowEmpty = element.allowEmpty ? element.allowEmpty : false;
        var extraCharacters = element.extraCharacters;
        if (validateFunc($(input).val(), allowEmpty, extraCharacters)) {
            $(input).removeClass(errorClass);
        } else {
            $(input).addClass(errorClass);
            validated = false;
        }
    }

    return validated;
}