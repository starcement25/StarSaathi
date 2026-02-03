@extends('layouts.default')
@section('main_container')
    <!-- page content -->
    <div class="right_col" role="main">
      <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>BID CENTER</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    {{ Form::open(array('url' => 'bidcenterreport','class'=>'form-horizontal form-label-left','id'=>'bidreportform')) }}
                    	<div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Choose Date:
                        </label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          {{ Form::text('start_date', null, array(
          								    'class' => 'form-control col-md-7 col-xs-12',
          								    'id' => 'start_date',
                                            'format' => 'DD-MM-YYYY',
          								    'placeholder' => 'Bid Date',
                                            'autocomplete' => 'off',
          								)) }}
                        </div>
                      </div>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          {{ Form::submit('Submit',array(
               								    'class' => 'btn btn-success',
               								    'id' => '',
               								    'placeholder' => '',
               								)) }}
                              <!--{{ Form::button('Export',array(
                                      'class' => 'btn btn-success bidbtn',
                                      'id' => '',
                                      'placeholder' => '',

                              )) }} !-->
                        </div>

                      </div>
                    {{ Form::close() }}
                  </div>
                </div>
              </div>
              @php if($reoprtdata) { @endphp
              <div class="clearfix"></div>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_content">
                    <div class="table-responsive">
                    	@if(!empty($successMsg))
                          <div class="alert alert-success"> {{ $successMsg }}</div>
                        @endif
                    <form name="update_bid_status" method="POST" action="/reportmvc/bidstatuschange">
                     {{ csrf_field() }}
                      <table class="table table-striped jambo_table bulk_action" id="display"  border="1">
                        <thead>
                          <tr class="headings">
                            <th class="column-title">Date </th>
                            <th class="column-title">Customer Name</th>
                            <th class="column-title">Prod Desc </th>
                            <th class="column-title">Bid Qty</th>
                            <th class="column-title">Bid Rate</th>
                            <th class="column-title">Base Rate</th>
                            <th class="column-title">Freight</th>
                            <th class="column-title">GST</th>
                            <th class="column-title">Counter Bid Rate</th>
                            <th class="column-title">Status</th>
                          </tr>
                        </thead>

                        <tbody>
                         <?php //echo "<pre>";print_r($reoprtdata);exit; ?>
                          @foreach ($reoprtdata as $key => $report)
                          		 @php
                               if($report->bid_status=='') $bid_status='CB';
                  				else					   $bid_status=$report->bid_status;	
                                @endphp				

                          <tr class="even pointer">
                            <td>{{$report->bid_date}}</td>
                            <td>{{$report->customer_name}}</td>
                            <td>{{$report->prod_desc}}</td>
                            <td align="right">{{$report->qty}}</td>
                            <td align="right">{{$report->bid_rate}}</td>
                            <td align="right">{{$report->base_rate}}</td>
                            <td align="right">{{($report->primary_freight+$report->secondary_freight+$report->depot_cost)}}</td>
                            <td align="right">{{$report->GST_value}}</td>
                            <td align="right">{{$report->counter_bid_rate}}</td>
                            @php if($bid_status=='REJECT'){@endphp
                             <td id="biddtatusradio{{$report->bid_id}}{{$report->prod_code}}" style="display:none"><input type="radio" name"bidstatuschange" 
                            value="ACCEPT" onchange="javascript:updatebidstatus(this.value,'{{$report->bid_id}}','{{$report->prod_code}}');"/>ACCEPT&nbsp;<input type="radio" name"bidstatuschange" value="REJECT" onchange="javascript:updatebidstatus(this.value,'{{$report->bid_id}}','{{$report->prod_code}}');"/>REJECT</td>
                            <td id="biddtatuslink{{$report->bid_id}}{{$report->prod_code}}" style="display:''" >
                            <a href="javascript:void(0);" onclick="javascript:display_bidstatusradio('{{$report->bid_id}}','{{$report->prod_code}}');">{{$bid_status}}</a></td>
                             @php }else{ @endphp
                             <td >{{$bid_status}}</td>
                             @php } @endphp
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                      <input type="hidden" name="bid_id" id="bid_id" value="" />
                      <input type="hidden" name="prod_code" id="prod_code" value="" />
                      <input type="hidden" name="bidstatus" id="bidstatus"  value="" />
                      <input type="hidden" name="startdateval" id="startdateval" value="" />
                      <form>
                      <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" align="center">
                              {{ Form::button('Export',array(
                                      'class' => 'btn btn-success bidbtn',
                                      'id' => '',
                                      'placeholder' => '',

                              )) }} 
                        </div>
                    </div>
                  </div>
                </div>
              </div>
                   
              @php } @endphp
    </div>
              <script type="text/javascript">
              $(".bidbtn").on("click", function(e){
                  e.preventDefault();
                  $('#bidreportform').attr('action', "{{ url('/bidcenterexportcsv') }}").submit();
              });
			  function display_bidstatusradio(bid_id,prod_code)
			  {
				  document.getElementById("biddtatuslink"+bid_id+prod_code).style.display='none';
				  document.getElementById("biddtatusradio"+bid_id+prod_code).style.display='';
			  }
			  function updatebidstatus(bidstatusval,bid_id,prod_code)
			  {
				  document.getElementById("bid_id").value=bid_id;
				  document.getElementById("prod_code").value=prod_code;
				  document.getElementById("bidstatus").value=bidstatusval;
				  document.getElementById("startdateval").value= document.getElementById("start_date").value;
        		  //document.update_bid_status.action = "/reportmvc/bidstatuschange";
				  document.update_bid_status.submit();
			  }
              </script>

    @include('includes/footer')
@endsection
