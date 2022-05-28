@extends('layouts.app')

@section('style')
<style>
    .error {
        color: red;
    }
    .tags {
        border: 1px solid #d1d1d1;
        border-radius: 3px;
        height: 40px;
    }
    input#tag_input {
        border: none;
        outline: none;
        padding-left: 5px;
        margin: 4px 0;
    }
    .tag_value {
        border: 1px solid #848484;
        border-radius: 5px;
        width: fit-content;
        padding: 0 10px;
        display: inline-block;
        margin-right: 5px;
    }
    button.close {
        border: 1px solid #bcbcbc;
        border-radius: 50%;
        padding: 0 5px;
        margin: 5px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Blog') }}</div>

                <div class="card-body">
                    <button type="button" class="btn btn-primary mb-3 add-blog" data-bs-toggle="modal" data-bs-target="#blogModal">
                        Add Blog
                    </button>

                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Tags</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($blogs as $blog)
                            <tr>
                                <td><img src="{{asset('/blog_image/' . $blog->image)}}" alt="No Image" width="100"></td>
                                <td>{{$blog->title}}</td>
                                <td>{{$blog->description}}</td>
                                <td>{{$blog->tags}}</td>
                                <td>
                                    <button type="button" class="btn btn-info edit" data-url="{{route('get-blog', $blog->id)}}">Edit</button>
                                    <button type="button" class="btn btn-danger delete" data-url="{{route('delete-blog', $blog->id)}}">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="blogModal" tabindex="-1" aria-labelledby="blogModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="blogModalLabel">Add BLog</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="blog-form" enctype='multipart/form-data'>
            <input type="hidden" name="data_id" id="data_id">
            
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" name="title" id="title">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" cols="10" rows="3" class="form-control" id="description"></textarea>
            </div>
            <div class="mb-3">
                <label for="tags" class="form-label">Tags</label>
                <div class="tags">
                    <input type="text" id="tag_input" onKeyPress="tagGenerate(event)">
                </div>
                <div class="tags_value"></div>

                <input type="hidden" name="tags" class="form-control" id="tags">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" name="image" class="form-control" id="image">
                <input type="hidden" name="old_image" class="form-control" id="old_image">
            </div>

            <button type="reset" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.btn-close').on('click', function(){
        $('#blogModalLabel').html('Add Blog');
        $('form').trigger('reset');
        $('#tags option').attr('selected', false);
    })

    $('.add-blog').on('click', function(){
        $('#blogModalLabel').html('Add Blog');
        $('form').trigger('reset');
        $('#tags option').attr('selected', false);
    })

    $('.edit').on('click', function(){
        $('label.error').css('display', 'none');
        $('input').removeClass('error');
        $('textarea').removeClass('error');

        var url = $(this).attr('data-url');

        $.ajax({
            url: url,
            type: 'get',
            success: function(response){
                
                $('#blogModalLabel').html('Edit Blog');
                
                $('#data_id').val(response.blog.id);
                $('#title').val(response.blog.title);
                $('#description').val(response.blog.description);
                $('#old_image').val(response.blog.image);
                
                $('.tags_value').find('div.tag_value').remove();
                $.each(response.blog.tags, function(key, value){
                    var elem = "<div class='tag_value'><label class='value'>" + value + "</label> <button type='button' class='close' onClick='remove(this)'>x</button></div>";
                    $('.tags_value').append(elem);
                })

                var tagValues = '';
                $.each($('.tag_value'), function(key, elem){
                    if(key == 0){
                        tagValues = $(elem).find('label.value').html();
                    }else{
                        tagValues = tagValues + ',' + $(elem).find('label.value').html();
                    }
                })

                $('#tags').val(tagValues);
                
                $('#blogModal').modal('show');
            }
        })
    })

    function tagGenerate(e){
        console.log(e);

        if(e.key == "Enter"){
            e.preventDefault();

            var val = $(e.target).val();
            $(e.target).val('');
            
            var elem = "<div class='tag_value'><label class='value'>" + val + "</label> <button type='button' class='close' onClick='remove(this)'>x</button></div>";

            $('.tags_value').append(elem);            

            var tagValues = '';
            $.each($('.tag_value'), function(key, elem){
                if(key == 0){
                    tagValues = $(elem).find('label.value').html();
                }else{
                    tagValues = tagValues + ',' + $(elem).find('label.value').html();
                }
            })

            $('#tags').val(tagValues);
        }
    }

    function remove(e){
        $(e).parent().remove();

        var tagValues = '';
        $.each($('.tag_value'), function(key, elem){
            if(key == 0){
                tagValues = $(elem).find('label.value').html();
            }else{
                tagValues = tagValues + ',' + $(elem).find('label.value').html();
            }
        })

        $('#tags').val(tagValues);
    }

    $(function(){
        $("form").validate({
            rules: {
                title : {
                    required: true,
                    maxlength: 255
                },
                description: {
                    required: true,
                    maxlength: 65535
                },
                tags: {
                    required: true,
                },
                image: {
                    required: {
                        depends: function(elem) {
                            if($('#data_id').val() == ''){

                                return true;
                            }
                        }
                    },
                }
            },
        });
    })

    $('form').on('submit', function(e){
        e.preventDefault();

        var id = $('#data_id').val();
        var url = '';
        var formData = new FormData(this);

        if(id == ''){
            url = "{{route('create-blog')}}"; 
        }else{
            url = "update-blog/" + id;
        }

        $.ajax({
            url: url,
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
                if(response.status == "success"){
                    alert(response.message);
                    
                    $('form').trigger('reset');
                    $('#tags option').attr('selected', false);
                    $('#blogModal').modal('hide');
    
                    location.reload();
                }

            }
        })
    })

    $('.delete').on('click', function(){
        var url = $(this).attr('data-url');

        $.ajax({
            url: url,
            type: 'get',
            success: function(response){
                if(response.status == "success"){
                    alert(response.message);
                    
                    location.reload();
                }

            }
        })
    })
</script>
@endsection
