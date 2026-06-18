import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import PrintLayout from "../../../Layouts/Print";
import ApplicationName from "../../../components/ApplicationName";
import Container from "../../../components/dashboard/Container";
import Table from "../../../components/dashboard/table/Table";
import ProductName from "../../../components/ProductName";
import { formatCurrency } from "../../../utils/formatAmount";
import useTranslation from "../../../hooks/useTranslation";

export default function ComissionPrint() {
    const { t } = useTranslation();
    const { nav = "earn", set = "com", rows = [], filters = {} } = usePage().props;
    const title = nav === "system" ? t("System Comissions") : set === "prof" ? t("Profits") : t("Earn Comissions");

    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 1000);

        return () => window.clearTimeout(timer);
    }, []);

    return (
        <PrintLayout title={title}>
            <div id="pdf-content">
                <Container>
                    <div className="text-center">
                        <h1>
                            <ApplicationName />
                        </h1>
                        <p>{title}</p>
                        {filters?.find ? <p>{t("Search")}: {filters.find}</p> : null}
                    </div>
                    <hr className="my-2" />

                    {nav === "earn" && set === "com" ? (
                        <Table data={rows}>
                            <thead>
                                <tr>
                                    <th>{t("#")}</th>
                                    <th>{t("ID")}</th>
                                    <th>{t("Source")}</th>
                                    <th>{t("Amount")}</th>
                                    <th>{t("Date")}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((earn, index) => (
                                    <tr key={earn.id}>
                                        <td>{index + 1}</td>
                                        <td>{earn.id}</td>
                                        <td><ProductName value={earn.product} /></td>
                                        <td>{formatCurrency(earn.amount)}</td>
                                        <td>{earn.date}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    ) : null}

                    {nav === "earn" && set === "prof" ? (
                        <Table data={rows}>
                            <thead>
                                <tr>
                                    <th>{t("#")}</th>
                                    <th>{t("ID")}</th>
                                    <th>{t("Product")}</th>
                                    <th>{t("Profit")}</th>
                                    <th>{t("Date")}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((earn, index) => (
                                    <tr key={earn.id}>
                                        <td>{index + 1}</td>
                                        <td>{earn.id}</td>
                                        <td><ProductName value={earn.product} /></td>
                                        <td>{formatCurrency(earn.profit)}</td>
                                        <td>{earn.date}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    ) : null}

                    {nav === "system" ? (
                        <Table data={rows}>
                            <thead>
                                <tr>
                                    <th>{t("#")}</th>
                                    <th>{t("Amount")}</th>
                                    <th>{t("Product")}</th>
                                    <th>{t("Order")}</th>
                                    <th>{t("Date")}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((take, index) => (
                                    <tr key={take.id}>
                                        <td>{index + 1}</td>
                                        <td>{formatCurrency(take.amount)}</td>
                                        <td><ProductName value={take.product} /></td>
                                        <td>{take.order}</td>
                                        <td>{take.date}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </Table>
                    ) : null}
                </Container>
            </div>
        </PrintLayout>
    );
}
