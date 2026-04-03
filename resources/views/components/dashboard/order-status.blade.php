<div>
    @props(['status'])
    @if ($status == 'Pending')
        <span class="px-2 py-1 bg-gray-300 rounded-lg">Pending</span>
    @endif

    @if ($status == 'Picked')
        <span class="px-2 py-1 text-white rounded-lg bg-sky-300">Picked</span>
    @endif

    @if ($status == 'Delivery')
        <span class="px-2 py-1 text-white rounded-lg bg-sky-300">Delivery</span>
    @endif

    @if ($status == 'Delivered')
        <span class="px-2 py-1 text-white bg-indigo-300 rounded-lg">Delivered</span>
    @endif

    @if ($status == 'Confirm')
        <span class="px-2 py-1 text-white bg-green-900 rounded-lg">Finished</span>
    @endif

    @if ($status == "Accept")
        <span class="px-2 py-1 text-white bg-indigo-900 rounded-lg">Accept</span>
    @endif

    @if ($status == "Cancel")
        <span class="px-2 py-1 bg-red-300 rounded-lg ">Reject</span>
    @endif
    @if ($status == "Hold")
        <span class="px-2 py-1 bg-red-300 rounded-lg ">Hold</span>
    @endif
    @if ($status == "Cancelled")
        <span class="px-2 py-1 text-white bg-gray-300 rounded-lg ">Buyer Cancelled</span>
    @endif
</div>
