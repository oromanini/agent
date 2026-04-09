import React from 'react';
import AppShell from '../Shared/AppShell';

const roleMap = {
    admin: 'Administrador',
    agent: 'Agente de vendas',
    technical: 'Responsável Técnico(a)',
    financial: 'Analista de financiamento',
    installer: 'Coordenador de instalação',
    contract: 'Gestor de contratos',
};

function formatBRL(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value || 0);
}

export default function Home({ dashboard, isAdmin, authUserPermission }) {
    return (
        <AppShell>
            <section className="inertia-grid">
                <div className="inertia-panel">
                    <h2 className="inertia-title">Bem-vindo ao novo painel</h2>
                    <p className="inertia-subtitle">
                        Interface mobile-first com React + Inertia para navegação rápida e moderna.
                    </p>
                    <span className="inertia-role">{roleMap[authUserPermission] || authUserPermission}</span>
                </div>

                {isAdmin && dashboard ? (
                    <>
                        <div className="inertia-kpi-grid">
                            <Card title="Orçamentos únicos (60d)" value={dashboard.proposals} />
                            <Card title="Ticket médio (60d)" value={formatBRL(dashboard.average_ticket)} />
                            <Card title="Total orçado (60d)" value={formatBRL(dashboard.total_sales)} />
                        </div>

                        <div className="inertia-list-grid">
                            <ListCard
                                title={`Propostas para aprovação (${dashboard.proposals_sent_count})`}
                                items={dashboard.proposals_sent_clients}
                                emptyText="Nenhuma proposta para aprovação."
                            />
                            <ListCard
                                title={`Propostas fechadas (${dashboard.closed_proposals_count})`}
                                items={dashboard.closed_proposals_clients}
                                emptyText="Nenhuma proposta fechada."
                            />
                        </div>
                    </>
                ) : (
                    <div className="inertia-panel">
                        <p className="inertia-subtitle">Seu painel personalizado está pronto para evolução em React/Inertia.</p>
                    </div>
                )}
            </section>
        </AppShell>
    );
}

function Card({ title, value }) {
    return (
        <article className="inertia-panel">
            <p className="inertia-kpi-title">{title}</p>
            <p className="inertia-kpi-value">{value}</p>
        </article>
    );
}

function ListCard({ title, items, emptyText }) {
    return (
        <article className="inertia-panel">
            <h3 className="inertia-list-title">{title}</h3>
            <ul className="inertia-list">
                {items?.length ? (
                    items.map((item, index) => (
                        <li key={`${item}-${index}`} className="inertia-list-item">
                            {item}
                        </li>
                    ))
                ) : (
                    <li className="inertia-subtitle">{emptyText}</li>
                )}
            </ul>
        </article>
    );
}
