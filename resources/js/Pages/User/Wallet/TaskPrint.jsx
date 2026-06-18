import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../Layouts/Print";
import ApplicationName from "../../../components/ApplicationName";
import Container from "../../../components/dashboard/Container";
import Table from "../../../components/dashboard/table/Table";
import useTranslation from "../../../hooks/useTranslation";
import { formatCurrency } from "../../../utils/formatAmount";

export default function TaskPrint() {
    const { t } = useTranslation();
    const { tasks = [], filters = {} } = usePage().props;

    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title={t("Your Tasks")}>
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>{t("Your Tasks")}</p>
                        {filters?.find ? <p>{t("Search")}: {filters.find}</p> : null}
                    </div>
                    <hr className="my-2" />

                    <Table data={tasks}>
                        <thead>
                            <tr>
                                <th>{t("#")}</th>
                                <th>{t("Date")}</th>
                                <th>{t("Earning")}</th>
                                <th>{t("Time")}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {tasks.map((item, index) => (
                                <tr key={item.id}>
                                    <td>{index + 1}</td>
                                    <td>{item.date}</td>
                                    <td>{formatCurrency(item.earning)}</td>
                                    <td>{item.time}</td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                </Container>
            </div>
        </PrintLayout>
    );
}
