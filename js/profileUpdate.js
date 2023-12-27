$(function() {
    let labelArr = [];
    let number = '';

    $('input[type="checkbox"]').change(function()
    {
        labelArr = $('input[type="checkbox"]:checked').map(function(){
            return $(this).siblings('label').text();
        }).get();
        mappedLabelArr = mappingLabelArr(labelArr);
        $('#checked_genre').html(mappedLabelArr);
    });


});

function mappingLabelArr(labelArr)
{
    beforeHtml = '<span class="text-sm text-sky-500 mx-2 px-2 my-1 bg-gray-100" >';
    afterHtml = '</span>';
    let mappedLabelArr = labelArr.map(function(element){
        return beforeHtml + element + afterHtml;
    });
    console.log(mappedLabelArr);
    return mappedLabelArr;
}

function previewImage(obj)
{
    var fileReader = new FileReader();
    fileReader.onload = (function(){
        document.getElementById('preview').src = fileReader.result;
    });
    fileReader.readAsDataURL(obj.files[0]);
}
