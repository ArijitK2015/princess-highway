//************************************************************************
//Submit review
function submitReviews()
{
    //check that all summary fields are filled
    var summaries = $$(".required-entry");
    var nb = summaries.length;
    var i;
    for (i=0;i<nb;i++)
    {
        if (summaries[i].value == '')
        {
            alert(fillFieldsMessage);
            return false;
        }
    }

    //check that all notes are filled
    var j;
    for (i=0;i<productIds.length;i++)
    {
        var productId = productIds[i];
        for (j=0;j<notesIds.length;j++)
        {
            var noteId = notesIds[j];
            var name = 'ratings[' + productId + '][notes][' + noteId + ']';

            var noteValue;
            var hasNote = false;
            for(noteValue=1;noteValue<=notesCount;noteValue++)
            {
                var id = name + '[' + noteValue + ']';
                if (document.getElementById(id).checked)
                    hasNote = true;
            }

            if (!hasNote)
            {
                alert(fillNotesMessage);
                return false;
            }
            
        }
    }

    //submit form
    document.getElementById('review-form').submit();
    
}