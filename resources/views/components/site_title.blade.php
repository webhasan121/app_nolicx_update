<div>
   <!-- I begin to speak only when I am certain what I will say is not better left unsaid. - Cato the Younger -->
   <title>
      @isset($title)
      {{$title}} |
      <x-application-name />
      @else
      {{config('app.name', 'nolicx')}}
      @endisset
   </title>
</div>