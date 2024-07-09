<!-- resources/views/create-signature.blade.php -->

<form method="POST" action="{{ route('store.user') }}">
    @csrf

    <input type="text" name="name" placeholder="Name">
    <input type="text" name="organization" placeholder="Organization">
    <input type="email" name="email" placeholder="Email">
    <input type="text" name="meeting_link" placeholder="Meeting Link">
    <input type="text" name="linkedin_profile" placeholder="LinkedIn Profile">
    <input type="text" name="website" placeholder="Website">
    <input type="text" name="phone" placeholder="Phone">
    <input type="text" name="address" placeholder="Address">

    <button type="submit">Save Signature</button>
</form>
