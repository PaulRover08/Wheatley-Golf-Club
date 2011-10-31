$(document).ready(function() {
    
    $('#field_type').change(function() {
        switch($(this).val())
        {
        case 'dropdown':
          $('#extradetails').show();
          break;
        default:
          $('#extradetails').hide();
        }
    });
    
    $('#addOption button').click(function() {
        var option = $('input[name="option"]').val();
        
        $('#optionList').append('<li>'+option+' <span class="removeOption">x</span></li>');
    });
    
});