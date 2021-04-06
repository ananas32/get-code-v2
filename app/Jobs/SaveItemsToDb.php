<?php

namespace App\Jobs;

use App\Models\Contractor;
use App\Models\Member;
use App\Models\MemberEmail;
use App\Models\MemberPhone;
use App\Models\Payment;
use App\Models\WorkHistory;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SaveItemsToDb implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rows;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($rows)
    {
        $this->rows = $rows;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $rows = $this->rows;
        foreach ($rows as $row) {
            DB::beginTransaction();
            try {
                foreach ($rows as $row) {
                    // save member
                    $member = Member::where('member_number', $row[2])->orWhere([
                        ['first_name', '=', $row[0]],
                        ['last_name', '=', $row[1]]
                    ])->first();

                    if (!$member) {
                        $member = new Member();
                        $member->first_name = $row[0];
                        $member->last_name = $row[1];
                        $member->member_number = $row[2];
                    }
                    $member->address = $row[5];
                    $member->city = $row[6];
                    $member->country = $row[7];
                    $member->save();
                    //end save member

                    //save member emails
                    $memberEmail = MemberEmail::where('email', $row[3])->first();
                    if (!$memberEmail) {
                        $memberEmail = new MemberEmail();
                        $memberEmail->email = $row[3];
                        $memberEmail->is_primary = 0;
                        $member->emails()->save($memberEmail);
                    }

                    $countMemberEmails = count($member->emails);
                    $i = 0;
                    foreach ($member->emails as $email) {
                        $email->is_primary = 0;
                        if (++$i === $countMemberEmails) {
                            $email->is_primary = 1;
                        }
                        $email->save();
                    }
                    // end save member emails

                    // save member phones
                    $memberPhone = MemberPhone::where('phone', $row[4])->first();
                    if (!$memberPhone) {
                        $memberPhone = new MemberPhone();
                        $memberPhone->phone = $row[4];
                        $memberPhone->is_primary = 0;
                        $member->phones()->save($memberPhone);
                    }
                    $countMemberPhones = count($member->phones);
                    $i = 0;
                    foreach ($member->phones as $phone) {
                        $phone->is_primary = 0;
                        if (++$i === $countMemberPhones) {
                            $phone->is_primary = 1;
                        }
                        $phone->save();
                    }
                    // end save member phones

                    //save contractor
                    $contractor = Contractor::firstOrCreate(['name' => $row[8]]);
                    // end save contractor

                    //save member work history
                    $workHistory = WorkHistory::where('start_date', $row[11])->first();
                    if (!$workHistory) {
                        $workHistory = new WorkHistory;
                        // need change this row
                        $workHistory->contractor_id = $contractor->id;
                        $workHistory->title = $row[9];
                        $workHistory->start_date = $row[10];
                        $workHistory->end_date = $row[11];
                    }
                    $workHistory->title = $row[9];
                    $member->workHistories()->save($workHistory);
                    //end save member work history

                    //save payments
                    $effectiveDate = Carbon::parse($row[10])->subMonth()->endOfMonth()->toDateString();
                    $paidDate = Carbon::parse($row[11])->addMonth()->startOfMonth()->toDateString();
                    $payment = new Payment();
                    $payment->amount = $row[12];
                    $payment->effective_date = $effectiveDate;
                    $payment->paid_date = $paidDate;
                    $member->payments()->save($payment);
                    //end save payments
                }

                DB::commit();
            } catch (\Exception $exception) {
                info('В строке: ' . $row . ' случилась ошибка, формата ', $exception);
                DB::rollBack();
            }
        }
    }
}
