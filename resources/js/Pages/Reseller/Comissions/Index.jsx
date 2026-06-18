import { Head } from "@inertiajs/react";
import { useEffect } from "react";
import PrintLayout from "../../../Layouts/Print";
import ApplicationName from "../../../components/ApplicationName";
import Container from "../../../components/dashboard/Container";
import Section from "../../../components/dashboard/section/Section";
import Table from "../../../components/dashboard/table/Table";
import useTranslation from "../../../hooks/useTranslation";
import { formatCurrency } from "../../../utils/formatAmount";

export default function Index({ filters = {}, comissions = [] }) {
    const { t } = useTranslation();
    useEffect(() => {
        const timer = window.setTimeout(() => {
            window.print();
        }, 500);

        return () => window.clearTimeout(timer);
    }, []);

    const where = filters?.where ?? "";
    const from = filters?.from_formatted ?? "";
    const to = filters?.to_formatted ?? "";

    return (
        <PrintLayout title={t("Comissions")}>
            <Head title={t("Comissions")} />
            <Container>
                <div className="w-ful text-center">
                    <div className="tex-xl">
                        <ApplicationName />
                    </div>
                    <div>
                        <p>{t("Comisstion Summery form")}{from}{t("to")}{to}</p>
                    </div>
                </div>
                <Section>
                    <Table data={comissions}>
                        <thead>
                            <tr>
                                <th>{t("ID")}</th>
                                {where === "user_id" ? <th>{t("Seller")}</th> : null}
                                {where === "order_id" ? <th>{t("Order")}</th> : null}
                                {where === "product_id" ? <th>{t("Product")}</th> : null}
                                <th>{t("Buy")}</th>
                                <th>{t("Sell")}</th>
                                <th>{t("Profit")}</th>
                                <th>{t("Rate")}</th>
                                <th>{t("Take")}</th>
                                <th>{t("Give")}</th>
                                <th>{t("Store")}</th>
                                <th>{t("Date")}</th>
                                <th>{t("Confirmed")}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {comissions.map((item) => (
                                <tr key={item.id}>
                                    <td>{item.id ?? "N/A"}</td>
                                    {where === "user_id" ? <th>{item.user_id}</th> : null}
                                    {where === "order_id" ? <td>{item.order_id ?? 0}</td> : null}
                                    {where === "product_id" ? <td>{item.product_id ?? 0}</td> : null}
                                    <td>{formatCurrency(item.buying_price)}</td>
                                    <td>{formatCurrency(item.selling_price)}</td>
                                    <td>{formatCurrency(item.profit)}</td>
                                    <td>{item.comission_range ?? "0"} %</td>
                                    <td>{formatCurrency(item.take_comission)}</td>
                                    <td>{formatCurrency(item.distribute_comission)}</td>
                                    <td>{formatCurrency(item.store)}</td>
                                    <td>{item.created_at_formatted}</td>
                                    <td>
                                        {item.confirmed ? (
                                            <span className="p-1 px-2 rounded-xl bg-green-900 text-white">{t("Confirmed")}</span>
                                        ) : (
                                            <span className="p-1 px-2 rounded-xl bg-gray-900 text-white">{t("Pending")}</span>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr className="py-2 bg-gray-200">
                                <td>{comissions.length}</td>
                                <td className="font-bold">{formatCurrency(comissions.reduce((sum, item) => sum + Number(item.buying_price || 0), 0))}</td>
                                <td className="font-bold">{formatCurrency(comissions.reduce((sum, item) => sum + Number(item.selling_price || 0), 0))}</td>
                                <td className="font-bold">{formatCurrency(comissions.reduce((sum, item) => sum + Number(item.profit || 0), 0))}</td>
                                <td></td>
                                <td className="font-bold">{formatCurrency(comissions.reduce((sum, item) => sum + Number(item.take_comission || 0), 0))}</td>
                                <td className="font-bold">{formatCurrency(comissions.reduce((sum, item) => sum + Number(item.distribute_comission || 0), 0))}</td>
                                <td className="font-bold">{formatCurrency(comissions.reduce((sum, item) => sum + Number(item.store || 0), 0))}</td>
                                <td className="font-bold"></td>
                                <td className="font-bold"></td>
                            </tr>
                        </tfoot>
                    </Table>
                </Section>
            </Container>
        </PrintLayout>
    );
}

