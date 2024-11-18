<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\User;

class UserController extends ResourceController
{
    protected $modelName = 'App\Models\User';
    protected $format    = 'json';

    // แสดงข้อมูลผู้ใช้ทั้งหมด
    public function index()
    {
        try {
            $data = $this->model->findAll();
            if (!$data) {
                return $this->failNotFound('ไม่พบข้อมูลผู้ใช้');
            }

            return $this->respond([
                'message' => 'success',
                'data_users' => $data
            ], 200);
        } catch (\Exception $e) {
            return $this->failServerError('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // แสดงข้อมูลผู้ใช้ตาม ID
    public function show($id = null)
    {
        if (is_null($id)) {
            return $this->failValidationError('ต้องระบุ ID ของผู้ใช้');
        }

        try {
            $user = $this->model->find($id);

            if (!$user) {
                return $this->failNotFound('ไม่พบผู้ใช้ที่มี ID ' . $id);
            }

            return $this->respond([
                'message' => 'success',
                'user' => $user // ส่งข้อมูลทั้งหมดที่มี
            ], 200);
        } catch (\Exception $e) {
            return $this->failServerError('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // สร้างข้อมูลผู้ใช้ใหม่
    public function create()
    {
        $input = $this->request->getJSON(); // รับ JSON
    
        // ตรวจสอบว่าข้อมูลครบถ้วนหรือไม่ (name, email, role ต้องไม่ว่าง)
        if (empty($input->name) || empty($input->email) || empty($input->role)) {
            return $this->failValidationError('ข้อมูลไม่ครบถ้วน (ต้องมี name, email, role)');
        }
    
        try {
            // ตรวจสอบว่ามีผู้ใช้ที่มี email นี้อยู่แล้วหรือไม่
            $existingUser = $this->model->where('email', $input->email)->first();
            if ($existingUser) {
                return $this->failConflict('มีผู้ใช้ที่มี email นี้อยู่แล้ว');
            }
    
            // สร้างผู้ใช้ใหม่
            $user = $this->model->save([
                'name'  => $input->name,
                'email' => $input->email,
                'role'  => $input->role,
            ]);
    
            if ($user) {
                return $this->respondCreated([
                    'message' => 'สร้างผู้ใช้สำเร็จ',
                    'user' => $input
                ]);
            } else {
                return $this->fail('ไม่สามารถสร้างผู้ใช้ได้');
            }
        } catch (\Exception $e) {
            return $this->failServerError('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
    


// อัปเดตข้อมูลผู้ใช้
public function update($id = null)
{
    if (is_null($id)) {
        return $this->failValidationError('ต้องระบุ ID ของผู้ใช้');
    }

    $input = $this->request->getJSON(true); // รับ JSON และแปลงเป็น array

    // ตรวจสอบว่ามีข้อมูลที่ต้องการอัปเดตหรือไม่
    if (!isset($input['name']) && !isset($input['email']) && !isset($input['role'])) {
        return $this->failValidationError('ต้องระบุข้อมูลที่ต้องการแก้ไข');
    }

    try {
        $user = $this->model->find($id);
        if (!$user) {
            return $this->failNotFound('ไม่พบผู้ใช้ที่มี ID ' . $id);
        }

        // อัปเดตข้อมูลผู้ใช้
        $updatedUser = $this->model->update($id, $input);

        if ($updatedUser) {
            return $this->respond([
                'message' => 'อัปเดตข้อมูลผู้ใช้สำเร็จ',
                'user' => $input
            ], 200);
        } else {
            return $this->fail('ไม่สามารถอัปเดตข้อมูลได้');
        }
    } catch (\Exception $e) {
        return $this->failServerError('เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}


    // ลบข้อมูลผู้ใช้
    public function delete($id = null)
    {
        if (is_null($id)) {
            return $this->failValidationError('ต้องระบุ ID ของผู้ใช้');
        }

        try {
            $user = $this->model->find($id);
            if (!$user) {
                return $this->failNotFound('ไม่พบผู้ใช้ที่มี ID ' . $id);
            }

            $this->model->delete($id);

            return $this->respondDeleted([
                'message' => 'ลบผู้ใช้สำเร็จ',
                'id' => $id
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // ฟังก์ชันจัดการข้อผิดพลาด: ซ้ำซ้อน (Conflict)
    protected function failConflict($message = 'Conflict occurred') {
        return $this->response->setStatusCode(409)->setJSON(['error' => $message]);
    }
}
