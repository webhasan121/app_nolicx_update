import DepositPrintSummery from "../deposit/PrintSummery";
import WithdrawPrint from "../withdraw/Print";
import PrintLayout from "../../../../Layouts/Print";

export default function Report({ nav, deposit = null, withdraw = null }) {
    if (nav === "Withdraw") {
        return (
            <WithdrawPrint
                filters={withdraw?.filters ?? {}}
                withdraws={withdraw?.withdraws ?? []}
                summary={withdraw?.summary ?? {}}
            />
        );
    }

    if (nav === "Deposit") {
        return (
            <DepositPrintSummery
                sdate={deposit?.sdate ?? ""}
                edate={deposit?.edate ?? ""}
                history={deposit?.history ?? { data: [], sum: 0 }}
            />
        );
    }

    return <PrintLayout title={`${nav ?? "Report"} Report`} />;
}
