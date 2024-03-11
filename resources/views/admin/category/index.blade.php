@extends('admin.admin_master')

@section('admin')

   
      <div class="container">
        
         
            <div class="card">

               

                <div class="card-body">

                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>{{ session('success') }}</strong>  
                            
                        </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-10"><h4 class="card-title">All Categories</h4></div>
                            <div class="col-md-2 text-right">
                                <a href="{{ route('create.category') }}">
                                    <button type="button" class="btn btn-gradient-primary btn-icon-text">
                                    <i class="mdi mdi-database-plus btn-icon-prepend"></i> Create </button>
                                </a>
                            </div>
                        </div>

                        <hr>
                        <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                <th>No.</th>
                                <th>Category Name</th>
                                <th>Category Image</th>
                                <th>User Name</th>
                                <th>Created At</th>
                                <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- @php($i = 1) -->
                                @foreach($categories as $category) 
                                <tr>
                                <th scope="row"> {{ $categories->firstItem()+$loop->index  }} </th>
                                <td> {{ $category->category_name }} </td>
                                <td> <img src="{{ asset($category->category_image) }}" style="height:40px; width:70px;" > </td> 
     
                                <td> {{ $category->user->name }} </td> 
                                <td> 
                                    @if($category->created_at ==  NULL)
                                    <span class="text-danger"> No Date Set</span> 
                                    @else
                                {{ Carbon\Carbon::parse($category->created_at)->diffForHumans() }}
                                    @endif
                                </td>
                                <td> 
                                <a href="{{ url('category/edit/'.$category->id) }}" class="btn btn-info">Edit</a>
                                <a href="{{ url('category/delete/'.$category->id) }}" onclick="return confirm('Are you sure to delete')" class="btn btn-danger">Delete</a>
       
                                    </td> 


                                </tr> 
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        
                        <div class="col-12 d-flex justify-content-center pt-4" class="li: { list-style: none; }">
                            {{ $categories->links() }}
                        </div>
                        

                </div>
                    
              </div>
           
        </div>
         
       
    

  
 @endsection
