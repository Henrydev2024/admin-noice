<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $members;

    public function __construct($members)
    {
        $this->members = $members;
    }

    public function collection()
    {
        $data = collect();
        foreach ($this->members as $member) {
            $data->push([
                $member->name,
                $member->total_records,
                $member->total_win,
                $member->total_loss,
                $member->total_amount,
                $member->total_amount_win,
                $member->total_amount_loss,
                $member->total_amount_win - $member->total_amount_loss,
                $member->total_fs_money,
            ]);
        }
        return $data;
    }

    public function headings(): array{
        return [
            [' ', 'Thống kê lệnh cược', ' ' , ' ','Thống kê tiền cược'],
            ['Người chơi', 'Số lượng cược', 'Thắng', 'Thua','Tổng tiền cược', 'Thắng', 'Thua', 'Thắng/Thua', 'Hoàn trả'],
        ];
    }
}
