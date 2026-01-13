<?php
/**
 * Shabab Setif - Committee Controller
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Committee;

class CommitteeController extends BaseController
{
    /**
     * List all committees
     */
    public function index(): void
    {
        $this->requireAuth();

        $this->view('committees/index', [
            'title' => 'إدارة اللجان',
            'layout' => 'main'
        ]);
    }

    /**
     * Get committees list (API)
     */
    public function list(): void
    {
        $this->requireAuth();

        $committees = Committee::allWithStats();

        $this->json([
            'success' => true,
            'data' => $committees
        ]);
    }

    /**
     * Get single committee
     */
    public function show(string $id): void
    {
        $this->requireAuth();

        $committee = Committee::find((int) $id);

        if (!$committee) {
            $this->json(['success' => false, 'message' => 'اللجنة غير موجودة'], 404);
        }

        $data = $committee->toArray();
        $data['members'] = $committee->members();
        $data['head'] = $committee->head()?->toArray();
        $data['member_count'] = $committee->memberCount();
        $data['activities'] = $committee->activities();

        $this->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store new committee
     */
    public function store(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $name = $this->input('name');

        if (empty($name)) {
            $this->json(['success' => false, 'message' => 'اسم اللجنة مطلوب'], 400);
        }

        $committee = Committee::create([
            'name' => $name,
            'description' => $this->input('description', '')
        ]);

        $this->json([
            'success' => true,
            'message' => 'تم إنشاء اللجنة بنجاح',
            'data' => $committee->toArray()
        ]);
    }

    /**
     * Update committee
     */
    public function update(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $committee = Committee::find((int) $id);

        if (!$committee) {
            $this->json(['success' => false, 'message' => 'اللجنة غير موجودة'], 404);
        }

        $data = [];

        if ($name = $this->input('name'))
            $data['name'] = $name;
        if ($description = $this->input('description'))
            $data['description'] = $description;

        if (!empty($data)) {
            $committee->update($data);
        }

        $this->json([
            'success' => true,
            'message' => 'تم تحديث اللجنة بنجاح'
        ]);
    }

    /**
     * Delete committee
     */
    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $committee = Committee::find((int) $id);

        if (!$committee) {
            $this->json(['success' => false, 'message' => 'اللجنة غير موجودة'], 404);
        }

        // Check if committee has members
        if ($committee->memberCount() > 0) {
            $this->json([
                'success' => false,
                'message' => 'لا يمكن حذف لجنة بها أعضاء. قم بنقل الأعضاء أولاً'
            ], 400);
        }

        $committee->delete();

        $this->json([
            'success' => true,
            'message' => 'تم حذف اللجنة بنجاح'
        ]);
    }
}
