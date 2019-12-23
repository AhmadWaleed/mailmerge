<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>MailMerge - Batches</title>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Recent Batches</div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th scope="col"># Batch ID</th>
                            <th scope="col">Default Service Used</th>
                            <th scope="col">Initially Processed At</th>
                            <th scope="col">Retried Services</th>
                            <th scope="col">Last Retried At</th>
                            <th scope="col">Select Retry Service</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($batches as $batch)
                            <form action="{{rout}}" METHOD="POST">
                                <input type="hidden" name="batch_id" value="{{$batch->id}}">
                                <tr aria-disabled="true">
                                    <th scope="row">{{$batch->batch_message_hash}}</th>
                                    <td>{{ucfirst($batch->service)}}</td>
                                    <td>{{$batch->created_at->diffForHumans()}}</td>
                                    <td>{{$batch->retries->pluck('service')->implode(',') ?: '---'}}</td>
                                    <td class="text-center">
                                        {{is_null($batch->last_retry) ? '---' : $batch->last_retry->retried_at->format('m-d-Y')}}
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <select name="service" class="custom-select"
                                                    id="inputGroupSelect01" {{is_null($batch->retried_at) ? '' : 'disabled'}}>
                                                <option disabled>Choose...</option>
                                                <option {{$batch->canRetry('mailgun') ? '' : 'disabled'}} value="mailgun">
                                                    Mailgun
                                                </option>
                                                <option {{$batch->canRetry('pepipost') ? '' : 'disabled'}} value="pepipost">
                                                    Pepipost
                                                </option>
                                                <option {{$batch->canRetry('sendgrid') ? '' : 'disabled'}} value="sendgrid">
                                                    Sendgrid
                                                </option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-success">Retry</button>
                                    </td>
                                </tr>
                            </form>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</body>
</html>