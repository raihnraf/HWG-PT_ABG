<h1>Book Borrowed Notification</h1>

<p>Dear {{ $bookLoan->user->name }},</p>

<p>You have borrowed the book "{{ $bookLoan->book->title }}" on {{ $bookLoan->borrowed_at->format('Y-m-d') }}.</p>

<p>The due date for return is {{ $bookLoan->due_date->format('Y-m-d') }}.</p>

<p>Thank you for using our library service!</p>
