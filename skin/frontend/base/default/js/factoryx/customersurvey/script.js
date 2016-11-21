function StarHover(num, sender, questionID)
{
    var goodStar = document.getElementById('goodstar').value;
    var badStar = document.getElementById('badstar').value;

    var nextStar;

    for(i = 1; i < 6; i++)
    {
        nextStar = document.getElementById('question' + questionID + '_' + i);

        if(i <= num)
        {
            nextStar.src = goodStar;
        }
        else
        {
            nextStar.src = badStar;
        }
    }
}

function starClick(num, questionID)
{
    document.getElementById('question' + questionID).value = num;
}

function starLeave(questionID)
{
    num = document.getElementById('question' + questionID).value;

    StarHover(num, null, questionID);
}

function ChangeHidden(id, answer)
{
    document.getElementById('question' + id).value = answer.value;
}

function CheckBoxAction(id, answer)
{
    var HiddenField = document.getElementById('question' + id);

    if(answer.checked == true)
    {
        HiddenField.value += " " + answer.value + ",";
    }
    else
    {
        HiddenField.value = HiddenField.value.replace(" " + answer.value + ",", "");
    }

}