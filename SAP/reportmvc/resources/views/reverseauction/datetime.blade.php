@extends('layouts.default')
@section('main_container')
  <!--link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet"-->
  <style>
	.row {
		display: flex;
		width: 70%;
		margin: 0 auto;
	}
/* Create two equal columns that sits next to each other */
	.column {
		flex: 50%;
		padding: 10px;
	}
	.datediv {
		flex: 50%;
		width: 50%;
		margin: 0 auto;
		text-align: center;
	}
	.main-container {
		width: 70%;
		border: 1px solid;
		margin: 0 auto;
		text-align: center;
		position:inherit;
		padding: 5px;
		margin-top:100px;
	}
</style>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>  
<div class="right_col" role="main">
  <form name="window_time" method="post" action="/reportmvc/windowtimesubmit" onsubmit="javascript:windowconfirm();" />
    {{ csrf_field() }}
    @if(session()->has('message'))
            <div class="alert alert-success" align="center" style="font-weight:bold;">
                {{ session()->get('message') }}
            </div>
        @endif
        @if (count($errors) > 0)
        <div class="alert alert-danger" align="center" style="font-weight:bold;">
            <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
            </ul>
        </div>
        @endif
   <div class="main-container">
   		 <h2 align="center"><u>Window Time</u></h2>
      <div class="datediv" style="position: relative">
        <p><strong>Date:</strong><input type="text" name="windowdate" class="datepicker form-control"/></p>
    </div>
    <div class="row" style="position: relative">
     <div class="column" >
        <p><strong>From:</strong><input type="text" name="timefrom" class="timepicker form-control" /></p>
      </div>
      <div class="column" >
       <p><strong>To:</strong><input type="text" name="timeto" class="timepicker form-control"/></p>
      </div>
    </div>
    	<div class="datediv">
        <p><input type="hidden" name="confirmval" id="confirmval" value=""/><input type="submit" name="timesubmit" value=" Submit "/></p>
    </div>
   </div> 
   </form>
    <!--div style="position: relative">
      <strong>From:</strong>
      <input class="timepicker form-control" type="text" name="from" size="11">
    </div-->
</div>
<script type="text/javascript">
    $('.timepicker').datetimepicker({
        format: 'HH:mm:ss'
    }); 
	 $('.datepicker').datetimepicker({
        format: 'DD-MM-YYYY'
    });
	function windowconfirm()
	{
		if(confirm("Is this last window time?"))
        {
			document.getElementById("confirmval").value='yes';
        	document.window_time.action = "/reportmvc/windowtimesubmit";
        }
        else
        {
			document.getElementById("confirmval").value='no';
      		document.window_time.action = "/reportmvc/windowtimesubmit";
        }
	}
</script>  
@include('includes/footer')


@endsection