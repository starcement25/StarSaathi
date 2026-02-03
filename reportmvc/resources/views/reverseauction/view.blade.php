@extends('layouts.default')

@section('main_container')

  <!--link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" /-->

    <!-- page content -->
    <div class="right_col" role="main">
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
		<table width="90%"   id="maintable" border="1" style="border-collapse: collapse;" align="center" cellpadding="10">
            <thead>
              <tr>
                <td colspan="12" align="center"><b><font size="+2" >Reverse Auction Price Released Rate</font></b></td>
              </tr>
              <tr  align="center" height="20">
                <td  width="5%" align="center"><b>Sl</b></td>
                 <td  width="9%" align="center"><b>SKU Code</b></td>
                <td  width="20%" style="background:#FFFFCC;" align="center"><b>SKU Name</b></td>
                <td width="11%" align="center"><b>Release Rate</b></td>
                <td width="13%" align="center" colspan="2"><b>Conversion</b></td>
                <td width="10%" align="center"><b>Base Rate</b></td>
                <td width="10%" align="center"><b>Indicative Rate</b></td>
                <td width="11%" align="center"><b>Counter Bid Limit<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(%)</b></td>
                 <td width="11%" align="center"><b>Counter Bid Rate</b></td>
              </tr>
              <tr align="center">
                <td   align="center"></td>
                <td  align="center"></td>
                <td  style="background:#FFFFCC;" align="center"></td>
                <td  align="center"></td>
                <td  align="center"><b>Operand</b></td>
                <td  align="center"><b>Value</b></td>
                <td  align="center"></td>
                <td  align="center"></td>
                <td  align="center"></td>
                 <td align="center"></td>
              </tr>
              @php $i=1; $plant_array=array(); @endphp
              @foreach($plantwisereleasedrate as $releasedrate)
              	 @php 
                 	$release_rate=$releasedrate->release_rate;
                    $base_rate=$releasedrate->base_rate;
                 	$indicative_rate=$releasedrate->indicative_rate;
                 	$counter_bid_limit=$releasedrate->counter_bid_limit;
                    $counter_bid_rate=$base_rate-($base_rate * $counter_bid_limit);
                    $addition=$releasedrate->addition;
                    $multiply=$releasedrate->multiply;
                    $conversion_type_multi='';
                    $conversion_val_multi='';
                    $conversion_type_add='';
                    $conversion_val_add='';
                    
                     if($multiply=='yes')
                    {
                    	$conversion_type_multi='(X)';
                        $conversion_val_multi=$releasedrate->conversion_two;
                    }
                    if($addition=='yes')
                    {
                    	$conversion_type_add='(+)';
                        $conversion_val_add=$releasedrate->conversion_one;
                    }
                   
                 
                  if(!in_array($releasedrate->plant_name,$plant_array))
                    {
                   @endphp  
                       <tr><td colspan='11' style="background:#FFCCCC;font-weight:bold;" align='center'>{{ $releasedrate->plant_name }}</td></tr>
                   @php   
                        array_push($plant_array,$releasedrate->plant_name);
                    }
                    @endphp 
              	<tr height="30">
                	<td  align="right" style="padding-right:2px">{{  $i  }}</td>
                    <td  align="right" style="padding-right:2px">{{  $releasedrate->prod_code  }}</td>
                	<td style="background:#FFFFCC; padding-left:5px;" >{{ $releasedrate->prod_desc  }}</td>
                    <td align="right" style="padding-right:2px">{{ $release_rate  }}</td>
                     <td align="center" style="padding-left:2px">{{ $conversion_type_multi }} <br /> {{ $conversion_type_add  }}</td>
                      <td align="right" style="padding-right:2px">{{ $conversion_val_multi }} <br /> {{ $conversion_val_add  }}</td>
                    <td align="right" style="padding-right:2px">{{ $base_rate  }}</td>
                    <td  align="right" style="padding-right:2px">{{ $indicative_rate  }}</td>
                    <td  align="right" style="padding-right:2px">{{ $counter_bid_limit  }}</td>
                    <td  align="right" style="padding-right:2px">{{ round($counter_bid_rate,0)  }}</td>
                 </tr>  
                 @php $i++; @endphp
               @endforeach
              </thead>
		</table>
        </form>
    </div>
    <!-- /page content -->
    @include('includes/footer')


@endsection
