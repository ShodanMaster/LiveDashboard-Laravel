<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Cool Bootstrap Dashboard</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT"
    crossorigin="anonymous"
  />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    rel="stylesheet"
  />
</head>
<body class="bg-light">

  <div class="container py-5">
    <h1 class="mb-5 text-center fw-bold">📊 Dashboard Overview</h1>

    <div class="row g-4 justify-content-center">

      <div class="col-md-5">
        <div class="card bg-primary bg-gradient text-white shadow-lg border-0 rounded-4">
          <div class="card-body text-center py-4">
            <div class="mb-3">
              <i class="bi bi-folder-fill display-4"></i>
            </div>
            <h5 class="card-title fw-light">Categories</h5>
            <span class="badge bg-light text-primary fs-6 px-3 py-2 categoryCount">{{$categoryCount}} Items</span>
          </div>
        </div>
      </div>

      <div class="col-md-5">
        <div class="card bg-success bg-gradient text-white shadow-lg border-0 rounded-4">
          <div class="card-body text-center py-4">
            <div class="mb-3">
              <i class="bi bi-box-seam display-4"></i>
            </div>
            <h5 class="card-title fw-light">Products</h5>
            <span class="badge bg-light text-success fs-6 px-3 py-2 productCount">{{$productCount}} Items</span>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
    crossorigin="anonymous"
  ></script>

  <script>
    var sseSource = new EventSource("{{ url('/dashboard-sse')}}");
    var randomNumber = 0;


    function establishSSEConnection(){
        sseSource = new EventSource("{{ url('/dashboard-sse')}}");

        sseSource.onmessage = function(event){
            let eventData = JSON.parse(event.data);

            if(eventData.randomNumber != randomNumber){
                randomNumber = eventData.randomNumber
                document.querySelector('.categoryCount').textContent = `${eventData.category} Items`;
                document.querySelector('.productCount').textContent = `${eventData.product} Items`;
            }

        };
        sseSource.onerror = function(event){
            if(sseSource.readyState == EventSource.CLOSED){
                console.log("Attempting to Reconnect");
                establishSSEConnection();
            }
        }
    }

    establishSSEConnection();
  </script>
</body>
</html>
