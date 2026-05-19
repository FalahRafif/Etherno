<section class="portfolio container" id="portfolio">
  @php $imgs = ['w1.jpg','w2.jpg','w3.jpg','w4.jpg','w5.jpg','w6.jpg']; @endphp
  @foreach($imgs as $img)
    <div><img src="{{ asset('assets/images/portfolio/' . $img) }}" alt="Portfolio"></div>
  @endforeach
</section>
