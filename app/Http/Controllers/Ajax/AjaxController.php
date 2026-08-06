<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Service;
use App\Models\Project;
use App\Models\Application;
use App\Models\ApplicationModule;
use App\Models\State;

class AjaxController extends Controller
{
        /**
     * State -> Services
     */
    public function services($stateId)
    {
        try {

            $services = Service::where('state_id', $stateId)
                ->where('is_active', 1)
                ->orderBy('service_name')
                ->get([
                    'service_id as id',
                    'service_name'
                ]);

                var_dump($services);

            return response()->json($services);

        } catch (\Exception $e) {

            Log::error('AjaxController::services => '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Unable to load services.'
            ],500);
        }
    }

    /**
     * Get active states
     */
    public function states()
    {
        try {
            $states = State::where('is_active', 1)
                ->orderBy('state_name')
                ->get([
                    'state_id as id',
                    'state_name'
                ]);

            return response()->json($states);

        } catch (\Exception $e) {
            Log::error('AjaxController::states => ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Unable to load states.'
            ], 500);
        }
    }

    /**
     * Service -> Projects
     */
    public function projects($serviceId)
    {
        try {

            $projects = Project::where('service_id',$serviceId)
                ->where('is_active',1)
                ->orderBy('project_name')
                ->get([
                    'project_id as id',
                    'project_name'
                ]);

            return response()->json($projects);

        } catch (\Exception $e) {

            Log::error('AjaxController::projects => '.$e->getMessage());

            return response()->json([
                'status'=>false,
                'message'=>'Unable to load projects.'
            ],500);

        }
    }

    /**
     * Project -> Applications
     */
    public function applications($projectId)
    {
        try {

            $applications = Application::where('project_id',$projectId)
                ->where('is_active',1)
                ->orderBy('application_name')
                ->get([
                    'application_id as id',
                    'application_name'
                ]);

            return response()->json($applications);

        } catch (\Exception $e) {

            Log::error('AjaxController::applications => '.$e->getMessage());

            return response()->json([
                'status'=>false,
                'message'=>'Unable to load applications.'
            ],500);

        }
    }

    /**
     * Application -> Modules
     */
    public function modules($applicationId)
    {
        try {

            $modules = ApplicationModule::where('application_id',$applicationId)
                ->where('is_active',1)
                ->orderBy('module_name')
                ->get([
                    'module_id as id',
                    'module_name'
                ]);

            return response()->json($modules);

        } catch (\Exception $e) {

            Log::error('AjaxController::modules => '.$e->getMessage());

            return response()->json([
                'status'=>false,
                'message'=>'Unable to load modules.'
            ],500);

        }
    }

    /**
     * Search Projects (Select2 AJAX)
     */
    public function searchProjects(Request $request)
    {
        try {

            $keyword = $request->keyword;

            $projects = Project::where('project_name','LIKE',"%{$keyword}%")
                ->where('is_active',1)
                ->limit(20)
                ->get([
                    'id',
                    'project_name'
                ]);

            return response()->json($projects);

        } catch (\Exception $e) {

            Log::error($e);

            return response()->json([],500);

        }
    }

    /**
     * Search Applications
     */
    public function searchApplications(Request $request)
    {
        try {

            $keyword = $request->keyword;

            $applications = Application::where('application_name','LIKE',"%{$keyword}%")
                ->where('is_active',1)
                ->limit(20)
                ->get([
                    'id',
                    'application_name'
                ]);

            return response()->json($applications);

        } catch (\Exception $e) {

            Log::error($e);

            return response()->json([],500);

        }
    }

    /**
     * Search Modules
     */
    public function searchModules(Request $request)
    {
        try {

            $keyword = $request->keyword;

            $modules = Module::where('module_name','LIKE',"%{$keyword}%")
                ->where('is_active',1)
                ->limit(20)
                ->get([
                    'id',
                    'module_name'
                ]);

            return response()->json($modules);

        } catch (\Exception $e) {

            Log::error($e);

            return response()->json([],500);

        }
    }
}