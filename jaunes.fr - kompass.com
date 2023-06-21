[1mdiff --git a/app/Http/Controllers/ProjectController.php b/app/Http/Controllers/ProjectController.php[m
[1mdeleted file mode 100644[m
[1mindex 53e3af8..0000000[m
[1m--- a/app/Http/Controllers/ProjectController.php[m
[1m+++ /dev/null[m
[36m@@ -1,66 +0,0 @@[m
[31m-<?php[m
[31m-[m
[31m-namespace App\Http\Controllers;[m
[31m-[m
[31m-use App\Models\Project;[m
[31m-use App\Http\Requests\StoreProjectRequest;[m
[31m-use App\Http\Requests\UpdateProjectRequest;[m
[31m-[m
[31m-class ProjectController extends Controller[m
[31m-{[m
[31m-    /**[m
[31m-     * Display a listing of the resource.[m
[31m-     */[m
[31m-    public function index()[m
[31m-    {[m
[31m-        //[m
[31m-    }[m
[31m-[m
[31m-    /**[m
[31m-     * Show the form for creating a new resource.[m
[31m-     */[m
[31m-    public function create()[m
[31m-    {[m
[31m-        //[m
[31m-    }[m
[31m-[m
[31m-    /**[m
[31m-     * Store a newly created resource in storage.[m
[31m-     */[m
[31m-    public function store(StoreProjectRequest $request)[m
[31m-    {[m
[31m-        //[m
[31m-    }[m
[31m-[m
[31m-    /**[m
[31m-     * Display the specified resource.[m
[31m-     */[m
[31m-    public function show(Project $project)[m
[31m-    {[m
[31m-        //[m
[31m-    }[m
[31m-[m
[31m-    /**[m
[31m-     * Show the form for editing the specified resource.[m
[31m-     */[m
[31m-    public function edit(Project $project)[m
[31m-    {[m
[31m-        //[m
[31m-    }[m
[31m-[m
[31m-    /**[m
[31m-     * Update the specified resource in storage.[m
[31m-     */[m
[31m-    public function update(UpdateProjectRequest $request, Project $project)[m
[31m-    {[m
[31m-        //[m
[31m-    }[m
[31m-[m
[31m-    /**[m
[31m-     * Remove the specified resource from storage.[m
[31m-     */[m
[31m-    public function destroy(Project $project)[m
[31m-    {[m
[31m-        //[m
[31m-    }[m
[31m-}[m
[1mdiff --git a/app/Http/Controllers/TypeProjetController.php b/app/Http/Controllers/TypeProjetController.php[m
[1mindex d9d1ce7..a1c296a 100644[m
[1m--- a/app/Http/Controllers/TypeProjetController.php[m
[1m+++ b/app/Http/Controllers/TypeProjetController.php[m
[36m@@ -41,9 +41,6 @@[m [mpublic function store(StoreTypeProjetRequest $request)[m
     {[m
         if (Auth::guard('api')->check() && (Auth::guard('api')->user()->type == 1 || Auth::guard('api')->user()->type == 2)) {[m
             [m
[31m-           [m
[31m-            [m
[31m-            [m
             $typeprojet = new typeprojet();[m
 [m
             $typeprojet->type = $request['type'];[m
