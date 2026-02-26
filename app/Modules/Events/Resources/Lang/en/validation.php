<?php

return [
    'title_required' => 'The event title is required',
    'title_max' => 'The event title must not exceed 255 characters',
    'title_unique' => 'An event with this title already exists',
    
    'category_required' => 'Please select a category',
    'category_exists' => 'The selected category is invalid',
    
    'description_required' => 'Please provide an event description',
    
    'venue_required' => 'The venue is required',
    'address_required' => 'The address is required',
    'city_required' => 'The city is required',
    'country_required' => 'The country is required',
    
    'start_date_required' => 'Please select a start date',
    'start_date_after' => 'The start date must be in the future',
    'end_date_required' => 'Please select an end date',
    'end_date_after' => 'The end date must be after the start date',
    
    'registration_deadline_before' => 'Registration deadline must be before the event starts',
    
    'max_attendees_integer' => 'Maximum attendees must be a number',
    'max_attendees_min' => 'Maximum attendees must be at least 1',
    
    'price_numeric' => 'Price must be a number',
    'price_min' => 'Price cannot be negative',
    
    'virtual_link_url' => 'Please provide a valid URL for the virtual event link',
    'virtual_link_required_if' => 'Virtual meeting link is required for virtual events',
    
    'media_image' => 'The file must be an image',
    'media_mimes' => 'The image must be a file of type: jpeg, png, jpg, gif',
    'media_max' => 'The image must not exceed 2MB',
];