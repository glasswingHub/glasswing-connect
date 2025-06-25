<?php

namespace App\Http\Controllers;

use App\Services\ProcessRegistration;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    /**
     * Example of using ProcessRegistration service directly
     */
    public function exampleUsage()
    {
        // Method 1: Using dependency injection
        $processRegistration = app(ProcessRegistration::class);
        
        // Method 2: Direct instantiation
        // $processRegistration = new ProcessRegistration();
        
        // Example data
        $registrationData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'company' => 'Example Corp',
            'position' => 'Developer'
        ];
        
        // Validate data first
        $validation = $processRegistration->validateData($registrationData);
        
        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'errors' => $validation['errors']
            ]);
        }
        
        // Process the registration
        $result = $processRegistration->execute($registrationData);
        
        return response()->json($result);
    }
    
    /**
     * Example of calling a custom stored procedure
     */
    public function customProcedureExample()
    {
        $processRegistration = app(ProcessRegistration::class);
        
        // Example of calling a custom stored procedure
        $result = $processRegistration->executeCustomProcedure(
            'sp_GetUserRegistrations',
            ['user_id' => 123, 'status' => 'active']
        );
        
        return response()->json($result);
    }
    
    /**
     * Example of batch processing
     */
    public function batchProcessingExample(Request $request)
    {
        $processRegistration = app(ProcessRegistration::class);
        $registrations = $request->input('registrations', []);
        $results = [];
        
        foreach ($registrations as $registration) {
            $validation = $processRegistration->validateData($registration);
            
            if ($validation['valid']) {
                $result = $processRegistration->execute($registration);
                $results[] = $result;
            } else {
                $results[] = [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation['errors']
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
} 