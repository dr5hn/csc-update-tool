<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function changeRequests()
    {
        return $this->hasMany(ChangeRequest::class);
    }

    public function approvedRequests()
    {
        return $this->hasMany(ChangeRequest::class, 'approved_by');
    }
}

// app/Models/ChangeRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'table_name',
        'change_type',
        'original_data',
        'new_data',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'original_data' => 'array',
        'new_data' => 'array',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function comments()
    {
        return $this->hasMany(ChangeRequestComment::class);
    }

    public function attachments()
    {
        return $this->hasMany(ChangeRequestAttachment::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}

// app/Models/ChangeRequestComment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeRequestComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'change_request_id',
        'user_id',
        'comment',
    ];

    public function changeRequest()
    {
        return $this->belongsTo(ChangeRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// app/Models/ChangeRequestAttachment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeRequestAttachment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'change_request_id',
        'filename',
        'original_filename',
        'mime_type',
        'file_size',
    ];

    public function changeRequest()
    {
        return $this->belongsTo(ChangeRequest::class);
    }
}

// app/Models/AuditLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
