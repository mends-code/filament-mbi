--
-- PostgreSQL database dump
--

-- Dumped from database version 15.7
-- Dumped by pg_dump version 15.6 (Debian 15.6-0+deb12u1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: chatwoot; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA chatwoot;


--
-- Name: cloudflare; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA cloudflare;


--
-- Name: google_vacuum_mgmt; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA google_vacuum_mgmt;


--
-- Name: mbi_chatwoot; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA mbi_chatwoot;


--
-- Name: mbi_filament; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA mbi_filament;


--
-- Name: mbi_link_shortener; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA mbi_link_shortener;


--
-- Name: mbi_stripe; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA mbi_stripe;


--
-- Name: patients; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA patients;


--
-- Name: public; Type: SCHEMA; Schema: -; Owner: -
--

-- *not* creating schema, since initdb creates it


--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA public IS '';


--
-- Name: google_vacuum_mgmt; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS google_vacuum_mgmt WITH SCHEMA google_vacuum_mgmt;


--
-- Name: EXTENSION google_vacuum_mgmt; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION google_vacuum_mgmt IS 'extension for assistive operational tooling';


--
-- Name: pg_stat_statements; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pg_stat_statements WITH SCHEMA public;


--
-- Name: EXTENSION pg_stat_statements; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION pg_stat_statements IS 'track planning and execution statistics of all SQL statements executed';


--
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


--
-- Name: accounts_after_insert_row_tr(); Type: FUNCTION; Schema: mbi_chatwoot; Owner: -
--

CREATE FUNCTION mbi_chatwoot.accounts_after_insert_row_tr() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    execute format('create sequence IF NOT EXISTS conv_dpid_seq_%s', NEW.id);
    RETURN NULL;
END;
$$;


--
-- Name: camp_dpid_before_insert(); Type: FUNCTION; Schema: mbi_chatwoot; Owner: -
--

CREATE FUNCTION mbi_chatwoot.camp_dpid_before_insert() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    execute format('create sequence IF NOT EXISTS camp_dpid_seq_%s', NEW.id);
    RETURN NULL;
END;
$$;


--
-- Name: campaigns_before_insert_row_tr(); Type: FUNCTION; Schema: mbi_chatwoot; Owner: -
--

CREATE FUNCTION mbi_chatwoot.campaigns_before_insert_row_tr() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.display_id := nextval('camp_dpid_seq_' || NEW.account_id);
    RETURN NEW;
END;
$$;


--
-- Name: conversations_before_insert_row_tr(); Type: FUNCTION; Schema: mbi_chatwoot; Owner: -
--

CREATE FUNCTION mbi_chatwoot.conversations_before_insert_row_tr() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.display_id := nextval('conv_dpid_seq_' || NEW.account_id);
    RETURN NEW;
END;
$$;


--
-- Name: n8n_trigger_function_4bdf0478_5c90_4354_95bb_899655c3715d(); Type: FUNCTION; Schema: mbi_chatwoot; Owner: -
--

CREATE FUNCTION mbi_chatwoot.n8n_trigger_function_4bdf0478_5c90_4354_95bb_899655c3715d() RETURNS trigger
    LANGUAGE plpgsql
    AS $$ begin perform pg_notify('n8n_channel_4bdf0478_5c90_4354_95bb_899655c3715d', row_to_json(new)::text); return null; end; $$;


--
-- Name: n8n_trigger_function_85defb4f_70f7_45ce_bb57_06dd774d85ba(); Type: FUNCTION; Schema: mbi_chatwoot; Owner: -
--

CREATE FUNCTION mbi_chatwoot.n8n_trigger_function_85defb4f_70f7_45ce_bb57_06dd774d85ba() RETURNS trigger
    LANGUAGE plpgsql
    AS $$ begin perform pg_notify('n8n_channel_85defb4f_70f7_45ce_bb57_06dd774d85ba', row_to_json(new)::text); return null; end; $$;


--
-- Name: prevent_delete(); Type: FUNCTION; Schema: mbi_chatwoot; Owner: -
--

CREATE FUNCTION mbi_chatwoot.prevent_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  RAISE EXCEPTION 'Deletion not allowed for this record';
END;
$$;


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: access_tokens; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.access_tokens (
    id bigint NOT NULL,
    owner_type character varying,
    owner_id bigint,
    token character varying,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: access_tokens_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.access_tokens_id_seq OWNED BY mbi_chatwoot.access_tokens.id;


--
-- Name: account_users; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.account_users (
    id bigint NOT NULL,
    account_id bigint,
    user_id bigint,
    role integer DEFAULT 0,
    inviter_id bigint,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    active_at timestamp without time zone,
    availability integer DEFAULT 0 NOT NULL,
    auto_offline boolean DEFAULT true NOT NULL
);


--
-- Name: account_users_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.account_users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: account_users_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.account_users_id_seq OWNED BY mbi_chatwoot.account_users.id;


--
-- Name: accounts; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.accounts (
    id integer NOT NULL,
    name character varying NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    locale integer DEFAULT 0,
    domain character varying(100),
    support_email character varying(100),
    feature_flags bigint DEFAULT 0 NOT NULL,
    auto_resolve_duration integer,
    limits jsonb DEFAULT '{}'::jsonb,
    custom_attributes jsonb DEFAULT '{}'::jsonb,
    status integer DEFAULT 0
);


--
-- Name: accounts_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.accounts_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: accounts_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.accounts_id_seq OWNED BY mbi_chatwoot.accounts.id;


--
-- Name: action_mailbox_inbound_emails; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.action_mailbox_inbound_emails (
    id bigint NOT NULL,
    status integer DEFAULT 0 NOT NULL,
    message_id character varying NOT NULL,
    message_checksum character varying NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: action_mailbox_inbound_emails_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.action_mailbox_inbound_emails_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: action_mailbox_inbound_emails_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.action_mailbox_inbound_emails_id_seq OWNED BY mbi_chatwoot.action_mailbox_inbound_emails.id;


--
-- Name: active_storage_attachments; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.active_storage_attachments (
    id bigint NOT NULL,
    name character varying NOT NULL,
    record_type character varying NOT NULL,
    record_id bigint NOT NULL,
    blob_id bigint NOT NULL,
    created_at timestamp without time zone NOT NULL
);


--
-- Name: active_storage_attachments_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.active_storage_attachments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: active_storage_attachments_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.active_storage_attachments_id_seq OWNED BY mbi_chatwoot.active_storage_attachments.id;


--
-- Name: active_storage_blobs; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.active_storage_blobs (
    id bigint NOT NULL,
    key character varying NOT NULL,
    filename character varying NOT NULL,
    content_type character varying,
    metadata text,
    byte_size bigint NOT NULL,
    checksum character varying,
    created_at timestamp without time zone NOT NULL,
    service_name character varying NOT NULL
);


--
-- Name: active_storage_blobs_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.active_storage_blobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: active_storage_blobs_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.active_storage_blobs_id_seq OWNED BY mbi_chatwoot.active_storage_blobs.id;


--
-- Name: active_storage_variant_records; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.active_storage_variant_records (
    id bigint NOT NULL,
    blob_id bigint NOT NULL,
    variation_digest character varying NOT NULL
);


--
-- Name: active_storage_variant_records_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.active_storage_variant_records_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: active_storage_variant_records_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.active_storage_variant_records_id_seq OWNED BY mbi_chatwoot.active_storage_variant_records.id;


--
-- Name: agent_bot_inboxes; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.agent_bot_inboxes (
    id bigint NOT NULL,
    inbox_id integer,
    agent_bot_id integer,
    status integer DEFAULT 0,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    account_id integer
);


--
-- Name: agent_bot_inboxes_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.agent_bot_inboxes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: agent_bot_inboxes_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.agent_bot_inboxes_id_seq OWNED BY mbi_chatwoot.agent_bot_inboxes.id;


--
-- Name: agent_bots; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.agent_bots (
    id bigint NOT NULL,
    name character varying,
    description character varying,
    outgoing_url character varying,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    account_id bigint,
    bot_type integer DEFAULT 0,
    bot_config jsonb DEFAULT '{}'::jsonb
);


--
-- Name: agent_bots_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.agent_bots_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: agent_bots_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.agent_bots_id_seq OWNED BY mbi_chatwoot.agent_bots.id;


--
-- Name: applied_slas; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.applied_slas (
    id bigint NOT NULL,
    account_id bigint NOT NULL,
    sla_policy_id bigint NOT NULL,
    conversation_id bigint NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    sla_status integer DEFAULT 0
);


--
-- Name: applied_slas_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.applied_slas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: applied_slas_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.applied_slas_id_seq OWNED BY mbi_chatwoot.applied_slas.id;


--
-- Name: ar_internal_metadata; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.ar_internal_metadata (
    key character varying NOT NULL,
    value character varying,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: articles; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.articles (
    id bigint NOT NULL,
    account_id integer NOT NULL,
    portal_id integer NOT NULL,
    category_id integer,
    folder_id integer,
    title character varying,
    description text,
    content text,
    status integer,
    views integer,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    author_id bigint,
    associated_article_id bigint,
    meta jsonb DEFAULT '{}'::jsonb,
    slug character varying NOT NULL,
    "position" integer
);


--
-- Name: articles_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.articles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: articles_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.articles_id_seq OWNED BY mbi_chatwoot.articles.id;


--
-- Name: attachments; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.attachments (
    id integer NOT NULL,
    file_type integer DEFAULT 0,
    external_url character varying,
    coordinates_lat double precision DEFAULT 0.0,
    coordinates_long double precision DEFAULT 0.0,
    message_id integer NOT NULL,
    account_id integer NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    fallback_title character varying,
    extension character varying
);


--
-- Name: attachments_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.attachments_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: attachments_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.attachments_id_seq OWNED BY mbi_chatwoot.attachments.id;


--
-- Name: audits; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.audits (
    id bigint NOT NULL,
    auditable_id bigint,
    auditable_type character varying,
    associated_id bigint,
    associated_type character varying,
    user_id bigint,
    user_type character varying,
    username character varying,
    action character varying,
    audited_changes jsonb,
    version integer DEFAULT 0,
    comment character varying,
    remote_address character varying,
    request_uuid character varying,
    created_at timestamp without time zone
);


--
-- Name: audits_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.audits_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: audits_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.audits_id_seq OWNED BY mbi_chatwoot.audits.id;


--
-- Name: automation_rules; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.automation_rules (
    id bigint NOT NULL,
    account_id bigint NOT NULL,
    name character varying NOT NULL,
    description text,
    event_name character varying NOT NULL,
    conditions jsonb DEFAULT '"{}"'::jsonb NOT NULL,
    actions jsonb DEFAULT '"{}"'::jsonb NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    active boolean DEFAULT true NOT NULL
);


--
-- Name: automation_rules_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.automation_rules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: automation_rules_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.automation_rules_id_seq OWNED BY mbi_chatwoot.automation_rules.id;


--
-- Name: camp_dpid_seq_1; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.camp_dpid_seq_1
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: camp_dpid_seq_2; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.camp_dpid_seq_2
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: camp_dpid_seq_3; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.camp_dpid_seq_3
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: camp_dpid_seq_6; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.camp_dpid_seq_6
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: camp_dpid_seq_7; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.camp_dpid_seq_7
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: campaigns; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.campaigns (
    id bigint NOT NULL,
    display_id integer NOT NULL,
    title character varying NOT NULL,
    description text,
    message text NOT NULL,
    sender_id integer,
    enabled boolean DEFAULT true,
    account_id bigint NOT NULL,
    inbox_id bigint NOT NULL,
    trigger_rules jsonb DEFAULT '{}'::jsonb,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    campaign_type integer DEFAULT 0 NOT NULL,
    campaign_status integer DEFAULT 0 NOT NULL,
    audience jsonb DEFAULT '[]'::jsonb,
    scheduled_at timestamp without time zone,
    trigger_only_during_business_hours boolean DEFAULT false
);


--
-- Name: campaigns_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.campaigns_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: campaigns_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.campaigns_id_seq OWNED BY mbi_chatwoot.campaigns.id;


--
-- Name: canned_responses; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.canned_responses (
    id integer NOT NULL,
    account_id integer NOT NULL,
    short_code character varying,
    content text,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL
);


--
-- Name: canned_responses_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.canned_responses_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: canned_responses_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.canned_responses_id_seq OWNED BY mbi_chatwoot.canned_responses.id;


--
-- Name: categories; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.categories (
    id bigint NOT NULL,
    account_id integer NOT NULL,
    portal_id integer NOT NULL,
    name character varying,
    description text,
    "position" integer,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    locale character varying DEFAULT 'en'::character varying,
    slug character varying NOT NULL,
    parent_category_id bigint,
    associated_category_id bigint,
    icon character varying DEFAULT ''::character varying
);


--
-- Name: categories_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.categories_id_seq OWNED BY mbi_chatwoot.categories.id;


--
-- Name: channel_api; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.channel_api (
    id bigint NOT NULL,
    account_id integer NOT NULL,
    webhook_url character varying,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    identifier character varying,
    hmac_token character varying,
    hmac_mandatory boolean DEFAULT false,
    additional_attributes jsonb DEFAULT '{}'::jsonb
);


--
-- Name: channel_api_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.channel_api_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: channel_api_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.channel_api_id_seq OWNED BY mbi_chatwoot.channel_api.id;


--
-- Name: channel_email; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.channel_email (
    id bigint NOT NULL,
    account_id integer NOT NULL,
    email character varying NOT NULL,
    forward_to_email character varying NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    imap_enabled boolean DEFAULT false,
    imap_address character varying DEFAULT ''::character varying,
    imap_port integer DEFAULT 0,
    imap_login character varying DEFAULT ''::character varying,
    imap_password character varying DEFAULT ''::character varying,
    imap_enable_ssl boolean DEFAULT true,
    smtp_enabled boolean DEFAULT false,
    smtp_address character varying DEFAULT ''::character varying,
    smtp_port integer DEFAULT 0,
    smtp_login character varying DEFAULT ''::character varying,
    smtp_password character varying DEFAULT ''::character varying,
    smtp_domain character varying DEFAULT ''::character varying,
    smtp_enable_starttls_auto boolean DEFAULT true,
    smtp_authentication character varying DEFAULT 'login'::character varying,
    smtp_openssl_verify_mode character varying DEFAULT 'none'::character varying,
    smtp_enable_ssl_tls boolean DEFAULT false,
    provider_config jsonb DEFAULT '{}'::jsonb,
    provider character varying
);


--
-- Name: channel_email_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.channel_email_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: channel_email_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.channel_email_id_seq OWNED BY mbi_chatwoot.channel_email.id;


--
-- Name: channel_facebook_pages; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.channel_facebook_pages (
    id integer NOT NULL,
    page_id character varying NOT NULL,
    user_access_token character varying NOT NULL,
    page_access_token character varying NOT NULL,
    account_id integer NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    instagram_id character varying
);


--
-- Name: channel_facebook_pages_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.channel_facebook_pages_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: channel_facebook_pages_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.channel_facebook_pages_id_seq OWNED BY mbi_chatwoot.channel_facebook_pages.id;


--
-- Name: channel_line; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.channel_line (
    id bigint NOT NULL,
    account_id integer NOT NULL,
    line_channel_id character varying NOT NULL,
    line_channel_secret character varying NOT NULL,
    line_channel_token character varying NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: channel_line_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.channel_line_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: channel_line_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.channel_line_id_seq OWNED BY mbi_chatwoot.channel_line.id;


--
-- Name: channel_sms; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.channel_sms (
    id bigint NOT NULL,
    account_id integer NOT NULL,
    phone_number character varying NOT NULL,
    provider character varying DEFAULT 'default'::character varying,
    provider_config jsonb DEFAULT '{}'::jsonb,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: channel_sms_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.channel_sms_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: channel_sms_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.channel_sms_id_seq OWNED BY mbi_chatwoot.channel_sms.id;


--
-- Name: channel_telegram; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.channel_telegram (
    id bigint NOT NULL,
    bot_name character varying,
    account_id integer NOT NULL,
    bot_token character varying NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: channel_telegram_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.channel_telegram_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: channel_telegram_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.channel_telegram_id_seq OWNED BY mbi_chatwoot.channel_telegram.id;


--
-- Name: channel_twilio_sms; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.channel_twilio_sms (
    id bigint NOT NULL,
    phone_number character varying,
    auth_token character varying NOT NULL,
    account_sid character varying NOT NULL,
    account_id integer NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    medium integer DEFAULT 0,
    messaging_service_sid character varying,
    api_key_sid character varying
);


--
-- Name: channel_twilio_sms_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.channel_twilio_sms_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: channel_twilio_sms_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.channel_twilio_sms_id_seq OWNED BY mbi_chatwoot.channel_twilio_sms.id;


--
-- Name: channel_twitter_profiles; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.channel_twitter_profiles (
    id bigint NOT NULL,
    profile_id character varying NOT NULL,
    twitter_access_token character varying NOT NULL,
    twitter_access_token_secret character varying NOT NULL,
    account_id integer NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    tweets_enabled boolean DEFAULT true
);


--
-- Name: channel_twitter_profiles_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.channel_twitter_profiles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: channel_twitter_profiles_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.channel_twitter_profiles_id_seq OWNED BY mbi_chatwoot.channel_twitter_profiles.id;


--
-- Name: channel_web_widgets; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.channel_web_widgets (
    id integer NOT NULL,
    website_url character varying,
    account_id integer,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    website_token character varying,
    widget_color character varying DEFAULT '#1f93ff'::character varying,
    welcome_title character varying,
    welcome_tagline character varying,
    feature_flags integer DEFAULT 7 NOT NULL,
    reply_time integer DEFAULT 0,
    hmac_token character varying,
    pre_chat_form_enabled boolean DEFAULT false,
    pre_chat_form_options jsonb DEFAULT '{}'::jsonb,
    hmac_mandatory boolean DEFAULT false,
    continuity_via_email boolean DEFAULT true NOT NULL
);


--
-- Name: channel_web_widgets_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.channel_web_widgets_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: channel_web_widgets_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.channel_web_widgets_id_seq OWNED BY mbi_chatwoot.channel_web_widgets.id;


--
-- Name: channel_whatsapp; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.channel_whatsapp (
    id bigint NOT NULL,
    account_id integer NOT NULL,
    phone_number character varying NOT NULL,
    provider character varying DEFAULT 'default'::character varying,
    provider_config jsonb DEFAULT '{}'::jsonb,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    message_templates jsonb DEFAULT '{}'::jsonb,
    message_templates_last_updated timestamp without time zone
);


--
-- Name: channel_whatsapp_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.channel_whatsapp_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: channel_whatsapp_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.channel_whatsapp_id_seq OWNED BY mbi_chatwoot.channel_whatsapp.id;


--
-- Name: contact_inboxes; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.contact_inboxes (
    id bigint NOT NULL,
    contact_id bigint,
    inbox_id bigint,
    source_id character varying NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    hmac_verified boolean DEFAULT false,
    pubsub_token character varying
);


--
-- Name: contact_inboxes_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.contact_inboxes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: contact_inboxes_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.contact_inboxes_id_seq OWNED BY mbi_chatwoot.contact_inboxes.id;


--
-- Name: contacts; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.contacts (
    id integer NOT NULL,
    name character varying DEFAULT ''::character varying,
    email character varying,
    phone_number character varying,
    account_id integer NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    additional_attributes jsonb DEFAULT '{}'::jsonb,
    identifier character varying,
    custom_attributes jsonb DEFAULT '{}'::jsonb,
    last_activity_at timestamp without time zone,
    contact_type integer DEFAULT 0,
    middle_name character varying DEFAULT ''::character varying,
    last_name character varying DEFAULT ''::character varying,
    location character varying DEFAULT ''::character varying,
    country_code character varying DEFAULT ''::character varying,
    blocked boolean DEFAULT false NOT NULL
);


--
-- Name: contacts_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.contacts_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: contacts_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.contacts_id_seq OWNED BY mbi_chatwoot.contacts.id;


--
-- Name: conv_dpid_seq_1; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.conv_dpid_seq_1
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: conv_dpid_seq_2; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.conv_dpid_seq_2
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: conv_dpid_seq_3; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.conv_dpid_seq_3
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: conv_dpid_seq_6; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.conv_dpid_seq_6
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: conv_dpid_seq_7; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.conv_dpid_seq_7
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: conversation_participants; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.conversation_participants (
    id bigint NOT NULL,
    account_id bigint NOT NULL,
    user_id bigint NOT NULL,
    conversation_id bigint NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: conversation_participants_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.conversation_participants_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: conversation_participants_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.conversation_participants_id_seq OWNED BY mbi_chatwoot.conversation_participants.id;


--
-- Name: conversations; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.conversations (
    id integer NOT NULL,
    account_id integer NOT NULL,
    inbox_id integer NOT NULL,
    status integer DEFAULT 0 NOT NULL,
    assignee_id integer,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    contact_id bigint,
    display_id integer NOT NULL,
    contact_last_seen_at timestamp without time zone,
    agent_last_seen_at timestamp without time zone,
    additional_attributes jsonb DEFAULT '{}'::jsonb,
    contact_inbox_id bigint,
    uuid uuid DEFAULT gen_random_uuid() NOT NULL,
    identifier character varying,
    last_activity_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    team_id bigint,
    campaign_id bigint,
    snoozed_until timestamp without time zone,
    custom_attributes jsonb DEFAULT '{}'::jsonb,
    assignee_last_seen_at timestamp without time zone,
    first_reply_created_at timestamp without time zone,
    priority integer,
    sla_policy_id bigint,
    waiting_since timestamp(6) without time zone,
    cached_label_list text
);


--
-- Name: conversations_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.conversations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: conversations_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.conversations_id_seq OWNED BY mbi_chatwoot.conversations.id;


--
-- Name: csat_survey_responses; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.csat_survey_responses (
    id bigint NOT NULL,
    account_id bigint NOT NULL,
    conversation_id bigint NOT NULL,
    message_id bigint NOT NULL,
    rating integer NOT NULL,
    feedback_message text,
    contact_id bigint NOT NULL,
    assigned_agent_id bigint,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: csat_survey_responses_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.csat_survey_responses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: csat_survey_responses_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.csat_survey_responses_id_seq OWNED BY mbi_chatwoot.csat_survey_responses.id;


--
-- Name: custom_attribute_definitions; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.custom_attribute_definitions (
    id bigint NOT NULL,
    attribute_display_name character varying,
    attribute_key character varying,
    attribute_display_type integer DEFAULT 0,
    default_value integer,
    attribute_model integer DEFAULT 0,
    account_id bigint,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    attribute_description text,
    attribute_values jsonb DEFAULT '[]'::jsonb,
    regex_pattern character varying,
    regex_cue character varying
);


--
-- Name: custom_attribute_definitions_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.custom_attribute_definitions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: custom_attribute_definitions_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.custom_attribute_definitions_id_seq OWNED BY mbi_chatwoot.custom_attribute_definitions.id;


--
-- Name: custom_filters; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.custom_filters (
    id bigint NOT NULL,
    name character varying NOT NULL,
    filter_type integer DEFAULT 0 NOT NULL,
    query jsonb DEFAULT '"{}"'::jsonb NOT NULL,
    account_id bigint NOT NULL,
    user_id bigint NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: custom_filters_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.custom_filters_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: custom_filters_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.custom_filters_id_seq OWNED BY mbi_chatwoot.custom_filters.id;


--
-- Name: dashboard_apps; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.dashboard_apps (
    id bigint NOT NULL,
    title character varying NOT NULL,
    content jsonb DEFAULT '[]'::jsonb,
    account_id bigint NOT NULL,
    user_id bigint,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: dashboard_apps_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.dashboard_apps_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: dashboard_apps_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.dashboard_apps_id_seq OWNED BY mbi_chatwoot.dashboard_apps.id;


--
-- Name: data_imports; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.data_imports (
    id bigint NOT NULL,
    account_id bigint NOT NULL,
    data_type character varying NOT NULL,
    status integer DEFAULT 0 NOT NULL,
    processing_errors text,
    total_records integer,
    processed_records integer,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: data_imports_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.data_imports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: data_imports_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.data_imports_id_seq OWNED BY mbi_chatwoot.data_imports.id;


--
-- Name: email_templates; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.email_templates (
    id bigint NOT NULL,
    name character varying NOT NULL,
    body text NOT NULL,
    account_id integer,
    template_type integer DEFAULT 1,
    locale integer DEFAULT 0 NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: email_templates_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.email_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: email_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.email_templates_id_seq OWNED BY mbi_chatwoot.email_templates.id;


--
-- Name: folders; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.folders (
    id bigint NOT NULL,
    account_id integer NOT NULL,
    category_id integer NOT NULL,
    name character varying,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: folders_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.folders_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: folders_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.folders_id_seq OWNED BY mbi_chatwoot.folders.id;


--
-- Name: inbox_members; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.inbox_members (
    id integer NOT NULL,
    user_id integer NOT NULL,
    inbox_id integer NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL
);


--
-- Name: inbox_members_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.inbox_members_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: inbox_members_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.inbox_members_id_seq OWNED BY mbi_chatwoot.inbox_members.id;


--
-- Name: inboxes; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.inboxes (
    id integer NOT NULL,
    channel_id integer NOT NULL,
    account_id integer NOT NULL,
    name character varying NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    channel_type character varying,
    enable_auto_assignment boolean DEFAULT true,
    greeting_enabled boolean DEFAULT false,
    greeting_message character varying,
    email_address character varying,
    working_hours_enabled boolean DEFAULT false,
    out_of_office_message character varying,
    timezone character varying DEFAULT 'UTC'::character varying,
    enable_email_collect boolean DEFAULT true,
    csat_survey_enabled boolean DEFAULT false,
    allow_messages_after_resolved boolean DEFAULT true,
    auto_assignment_config jsonb DEFAULT '{}'::jsonb,
    lock_to_single_conversation boolean DEFAULT false NOT NULL,
    portal_id bigint,
    sender_name_type integer DEFAULT 0 NOT NULL,
    business_name character varying
);


--
-- Name: inboxes_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.inboxes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: inboxes_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.inboxes_id_seq OWNED BY mbi_chatwoot.inboxes.id;


--
-- Name: installation_configs; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.installation_configs (
    id bigint NOT NULL,
    name character varying NOT NULL,
    serialized_value jsonb DEFAULT '{}'::jsonb NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    locked boolean DEFAULT true NOT NULL
);


--
-- Name: installation_configs_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.installation_configs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: installation_configs_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.installation_configs_id_seq OWNED BY mbi_chatwoot.installation_configs.id;


--
-- Name: integrations_hooks; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.integrations_hooks (
    id bigint NOT NULL,
    status integer DEFAULT 1,
    inbox_id integer,
    account_id integer,
    app_id character varying,
    hook_type integer DEFAULT 0,
    reference_id character varying,
    access_token character varying,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    settings jsonb DEFAULT '{}'::jsonb
);


--
-- Name: integrations_hooks_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.integrations_hooks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: integrations_hooks_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.integrations_hooks_id_seq OWNED BY mbi_chatwoot.integrations_hooks.id;


--
-- Name: labels; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.labels (
    id bigint NOT NULL,
    title character varying,
    description text,
    color character varying DEFAULT '#1f93ff'::character varying NOT NULL,
    show_on_sidebar boolean,
    account_id bigint,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: labels_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.labels_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: labels_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.labels_id_seq OWNED BY mbi_chatwoot.labels.id;


--
-- Name: macros; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.macros (
    id bigint NOT NULL,
    account_id bigint NOT NULL,
    name character varying NOT NULL,
    visibility integer DEFAULT 0,
    created_by_id bigint,
    updated_by_id bigint,
    actions jsonb DEFAULT '{}'::jsonb NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: macros_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.macros_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: macros_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.macros_id_seq OWNED BY mbi_chatwoot.macros.id;


--
-- Name: mentions; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.mentions (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    conversation_id bigint NOT NULL,
    account_id bigint NOT NULL,
    mentioned_at timestamp without time zone NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: mentions_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.mentions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: mentions_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.mentions_id_seq OWNED BY mbi_chatwoot.mentions.id;


--
-- Name: messages; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.messages (
    id integer NOT NULL,
    content text,
    account_id integer NOT NULL,
    inbox_id integer NOT NULL,
    conversation_id integer NOT NULL,
    message_type integer NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    private boolean DEFAULT false NOT NULL,
    status integer DEFAULT 0,
    source_id character varying,
    content_type integer DEFAULT 0 NOT NULL,
    content_attributes json DEFAULT '{}'::json,
    sender_type character varying,
    sender_id bigint,
    external_source_ids jsonb DEFAULT '{}'::jsonb,
    additional_attributes jsonb DEFAULT '{}'::jsonb,
    processed_message_content text,
    sentiment jsonb DEFAULT '{}'::jsonb
);


--
-- Name: messages_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.messages_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: messages_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.messages_id_seq OWNED BY mbi_chatwoot.messages.id;


--
-- Name: nc_evolutions; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.nc_evolutions (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    "titleDown" character varying(255),
    description character varying(255),
    batch integer,
    checksum character varying(255),
    status integer,
    created timestamp with time zone,
    created_at timestamp with time zone,
    updated_at timestamp with time zone
);


--
-- Name: nc_evolutions_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.nc_evolutions_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: nc_evolutions_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.nc_evolutions_id_seq OWNED BY mbi_chatwoot.nc_evolutions.id;


--
-- Name: notes; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.notes (
    id bigint NOT NULL,
    content text NOT NULL,
    account_id bigint NOT NULL,
    contact_id bigint NOT NULL,
    user_id bigint,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: notes_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.notes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: notes_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.notes_id_seq OWNED BY mbi_chatwoot.notes.id;


--
-- Name: notification_settings; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.notification_settings (
    id bigint NOT NULL,
    account_id integer,
    user_id integer,
    email_flags integer DEFAULT 0 NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    push_flags integer DEFAULT 0 NOT NULL
);


--
-- Name: notification_settings_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.notification_settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: notification_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.notification_settings_id_seq OWNED BY mbi_chatwoot.notification_settings.id;


--
-- Name: notification_subscriptions; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.notification_subscriptions (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    subscription_type integer NOT NULL,
    subscription_attributes jsonb DEFAULT '{}'::jsonb NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    identifier text
);


--
-- Name: notification_subscriptions_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.notification_subscriptions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: notification_subscriptions_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.notification_subscriptions_id_seq OWNED BY mbi_chatwoot.notification_subscriptions.id;


--
-- Name: notifications; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.notifications (
    id bigint NOT NULL,
    account_id bigint NOT NULL,
    user_id bigint NOT NULL,
    notification_type integer NOT NULL,
    primary_actor_type character varying NOT NULL,
    primary_actor_id bigint NOT NULL,
    secondary_actor_type character varying,
    secondary_actor_id bigint,
    read_at timestamp without time zone,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    snoozed_until timestamp(6) without time zone,
    last_activity_at timestamp(6) without time zone DEFAULT CURRENT_TIMESTAMP,
    meta jsonb DEFAULT '{}'::jsonb
);


--
-- Name: notifications_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.notifications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: notifications_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.notifications_id_seq OWNED BY mbi_chatwoot.notifications.id;


--
-- Name: platform_app_permissibles; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.platform_app_permissibles (
    id bigint NOT NULL,
    platform_app_id bigint NOT NULL,
    permissible_type character varying NOT NULL,
    permissible_id bigint NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: platform_app_permissibles_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.platform_app_permissibles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: platform_app_permissibles_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.platform_app_permissibles_id_seq OWNED BY mbi_chatwoot.platform_app_permissibles.id;


--
-- Name: platform_apps; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.platform_apps (
    id bigint NOT NULL,
    name character varying NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: platform_apps_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.platform_apps_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: platform_apps_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.platform_apps_id_seq OWNED BY mbi_chatwoot.platform_apps.id;


--
-- Name: portal_members; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.portal_members (
    id bigint NOT NULL,
    portal_id bigint,
    user_id bigint,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: portal_members_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.portal_members_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: portal_members_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.portal_members_id_seq OWNED BY mbi_chatwoot.portal_members.id;


--
-- Name: portals; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.portals (
    id bigint NOT NULL,
    account_id integer NOT NULL,
    name character varying NOT NULL,
    slug character varying NOT NULL,
    custom_domain character varying,
    color character varying,
    homepage_link character varying,
    page_title character varying,
    header_text text,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    config jsonb DEFAULT '{"allowed_locales": ["en"]}'::jsonb,
    archived boolean DEFAULT false,
    channel_web_widget_id bigint
);


--
-- Name: portals_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.portals_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: portals_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.portals_id_seq OWNED BY mbi_chatwoot.portals.id;


--
-- Name: portals_members; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.portals_members (
    portal_id bigint NOT NULL,
    user_id bigint NOT NULL
);


--
-- Name: related_categories; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.related_categories (
    id bigint NOT NULL,
    category_id bigint,
    related_category_id bigint,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: related_categories_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.related_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: related_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.related_categories_id_seq OWNED BY mbi_chatwoot.related_categories.id;


--
-- Name: reporting_events; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.reporting_events (
    id bigint NOT NULL,
    name character varying,
    value double precision,
    account_id integer,
    inbox_id integer,
    user_id integer,
    conversation_id integer,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    value_in_business_hours double precision,
    event_start_time timestamp without time zone,
    event_end_time timestamp without time zone
);


--
-- Name: reporting_events_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.reporting_events_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: reporting_events_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.reporting_events_id_seq OWNED BY mbi_chatwoot.reporting_events.id;


--
-- Name: role_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.role_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: schema_migrations; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.schema_migrations (
    version character varying NOT NULL
);


--
-- Name: sla_events; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.sla_events (
    id bigint NOT NULL,
    applied_sla_id bigint NOT NULL,
    conversation_id bigint NOT NULL,
    account_id bigint NOT NULL,
    sla_policy_id bigint NOT NULL,
    inbox_id bigint NOT NULL,
    event_type integer,
    meta jsonb DEFAULT '{}'::jsonb,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: sla_events_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.sla_events_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: sla_events_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.sla_events_id_seq OWNED BY mbi_chatwoot.sla_events.id;


--
-- Name: sla_policies; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.sla_policies (
    id bigint NOT NULL,
    name character varying NOT NULL,
    first_response_time_threshold double precision,
    next_response_time_threshold double precision,
    only_during_business_hours boolean DEFAULT false,
    account_id bigint NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    description character varying,
    resolution_time_threshold double precision
);


--
-- Name: sla_policies_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.sla_policies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: sla_policies_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.sla_policies_id_seq OWNED BY mbi_chatwoot.sla_policies.id;


--
-- Name: taggings; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.taggings (
    id integer NOT NULL,
    tag_id integer,
    taggable_type character varying,
    taggable_id integer,
    tagger_type character varying,
    tagger_id integer,
    context character varying(128),
    created_at timestamp without time zone
);


--
-- Name: taggings_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.taggings_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: taggings_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.taggings_id_seq OWNED BY mbi_chatwoot.taggings.id;


--
-- Name: tags; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.tags (
    id integer NOT NULL,
    name character varying,
    taggings_count integer DEFAULT 0
);


--
-- Name: tags_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.tags_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tags_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.tags_id_seq OWNED BY mbi_chatwoot.tags.id;


--
-- Name: team_members; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.team_members (
    id bigint NOT NULL,
    team_id bigint NOT NULL,
    user_id bigint NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: team_members_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.team_members_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: team_members_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.team_members_id_seq OWNED BY mbi_chatwoot.team_members.id;


--
-- Name: teams; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.teams (
    id bigint NOT NULL,
    name character varying NOT NULL,
    description text,
    allow_auto_assign boolean DEFAULT true,
    account_id bigint NOT NULL,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


--
-- Name: teams_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.teams_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: teams_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.teams_id_seq OWNED BY mbi_chatwoot.teams.id;


--
-- Name: telegram_bots; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.telegram_bots (
    id integer NOT NULL,
    name character varying,
    auth_key character varying,
    account_id integer,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL
);


--
-- Name: telegram_bots_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.telegram_bots_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: telegram_bots_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.telegram_bots_id_seq OWNED BY mbi_chatwoot.telegram_bots.id;


--
-- Name: users; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.users (
    id integer NOT NULL,
    provider character varying DEFAULT 'email'::character varying NOT NULL,
    uid character varying DEFAULT ''::character varying NOT NULL,
    encrypted_password character varying DEFAULT ''::character varying NOT NULL,
    reset_password_token character varying,
    reset_password_sent_at timestamp without time zone,
    remember_created_at timestamp without time zone,
    sign_in_count integer DEFAULT 0 NOT NULL,
    current_sign_in_at timestamp without time zone,
    last_sign_in_at timestamp without time zone,
    current_sign_in_ip character varying,
    last_sign_in_ip character varying,
    confirmation_token character varying,
    confirmed_at timestamp without time zone,
    confirmation_sent_at timestamp without time zone,
    unconfirmed_email character varying,
    name character varying NOT NULL,
    display_name character varying,
    email character varying,
    tokens json,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    pubsub_token character varying,
    availability integer DEFAULT 0,
    ui_settings jsonb DEFAULT '{}'::jsonb,
    custom_attributes jsonb DEFAULT '{}'::jsonb,
    type character varying,
    message_signature text
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.users_id_seq OWNED BY mbi_chatwoot.users.id;


--
-- Name: webhooks; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.webhooks (
    id bigint NOT NULL,
    account_id integer,
    inbox_id integer,
    url character varying,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    webhook_type integer DEFAULT 0,
    subscriptions jsonb DEFAULT '["conversation_status_changed", "conversation_updated", "conversation_created", "contact_created", "contact_updated", "message_created", "message_updated", "webwidget_triggered"]'::jsonb
);


--
-- Name: webhooks_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.webhooks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: webhooks_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.webhooks_id_seq OWNED BY mbi_chatwoot.webhooks.id;


--
-- Name: working_hours; Type: TABLE; Schema: mbi_chatwoot; Owner: -
--

CREATE TABLE mbi_chatwoot.working_hours (
    id bigint NOT NULL,
    inbox_id bigint,
    account_id bigint,
    day_of_week integer NOT NULL,
    closed_all_day boolean DEFAULT false,
    open_hour integer,
    open_minutes integer,
    close_hour integer,
    close_minutes integer,
    created_at timestamp(6) without time zone NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL,
    open_all_day boolean DEFAULT false
);


--
-- Name: working_hours_id_seq; Type: SEQUENCE; Schema: mbi_chatwoot; Owner: -
--

CREATE SEQUENCE mbi_chatwoot.working_hours_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: working_hours_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_chatwoot; Owner: -
--

ALTER SEQUENCE mbi_chatwoot.working_hours_id_seq OWNED BY mbi_chatwoot.working_hours.id;


--
-- Name: cache; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: chatwoot_contacts_patients; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.chatwoot_contacts_patients (
    chatwoot_contact_id bigint NOT NULL,
    patient_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: doctors; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.doctors (
    id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: doctors_id_seq; Type: SEQUENCE; Schema: mbi_filament; Owner: -
--

CREATE SEQUENCE mbi_filament.doctors_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: doctors_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_filament; Owner: -
--

ALTER SEQUENCE mbi_filament.doctors_id_seq OWNED BY mbi_filament.doctors.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: mbi_filament; Owner: -
--

CREATE SEQUENCE mbi_filament.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_filament; Owner: -
--

ALTER SEQUENCE mbi_filament.failed_jobs_id_seq OWNED BY mbi_filament.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: mbi_filament; Owner: -
--

CREATE SEQUENCE mbi_filament.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_filament; Owner: -
--

ALTER SEQUENCE mbi_filament.jobs_id_seq OWNED BY mbi_filament.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: mbi_filament; Owner: -
--

CREATE SEQUENCE mbi_filament.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_filament; Owner: -
--

ALTER SEQUENCE mbi_filament.migrations_id_seq OWNED BY mbi_filament.migrations.id;


--
-- Name: notifications; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.notifications (
    id uuid NOT NULL,
    type character varying(255) NOT NULL,
    notifiable_type character varying(255) NOT NULL,
    notifiable_id bigint NOT NULL,
    data json NOT NULL,
    read_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: password_reset_tokens; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: patients; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.patients (
    id bigint NOT NULL,
    first_name character varying(255) NOT NULL,
    last_name character varying(255) NOT NULL,
    birthdate date NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    created_by bigint,
    updated_by bigint
);


--
-- Name: patients_id_seq; Type: SEQUENCE; Schema: mbi_filament; Owner: -
--

CREATE SEQUENCE mbi_filament.patients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: patients_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_filament; Owner: -
--

ALTER SEQUENCE mbi_filament.patients_id_seq OWNED BY mbi_filament.patients.id;


--
-- Name: prescription_templates; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.prescription_templates (
    id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: prescription_templates_id_seq; Type: SEQUENCE; Schema: mbi_filament; Owner: -
--

CREATE SEQUENCE mbi_filament.prescription_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: prescription_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_filament; Owner: -
--

ALTER SEQUENCE mbi_filament.prescription_templates_id_seq OWNED BY mbi_filament.prescription_templates.id;


--
-- Name: pulse_aggregates; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.pulse_aggregates (
    id bigint NOT NULL,
    bucket integer NOT NULL,
    period integer NOT NULL,
    type character varying(255) NOT NULL,
    key text NOT NULL,
    key_hash uuid GENERATED ALWAYS AS ((md5(key))::uuid) STORED NOT NULL,
    aggregate character varying(255) NOT NULL,
    value numeric(20,2) NOT NULL,
    count integer
);


--
-- Name: pulse_aggregates_id_seq; Type: SEQUENCE; Schema: mbi_filament; Owner: -
--

CREATE SEQUENCE mbi_filament.pulse_aggregates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: pulse_aggregates_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_filament; Owner: -
--

ALTER SEQUENCE mbi_filament.pulse_aggregates_id_seq OWNED BY mbi_filament.pulse_aggregates.id;


--
-- Name: pulse_entries; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.pulse_entries (
    id bigint NOT NULL,
    "timestamp" integer NOT NULL,
    type character varying(255) NOT NULL,
    key text NOT NULL,
    key_hash uuid GENERATED ALWAYS AS ((md5(key))::uuid) STORED NOT NULL,
    value bigint
);


--
-- Name: pulse_entries_id_seq; Type: SEQUENCE; Schema: mbi_filament; Owner: -
--

CREATE SEQUENCE mbi_filament.pulse_entries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: pulse_entries_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_filament; Owner: -
--

ALTER SEQUENCE mbi_filament.pulse_entries_id_seq OWNED BY mbi_filament.pulse_entries.id;


--
-- Name: pulse_values; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.pulse_values (
    id bigint NOT NULL,
    "timestamp" integer NOT NULL,
    type character varying(255) NOT NULL,
    key text NOT NULL,
    key_hash uuid GENERATED ALWAYS AS ((md5(key))::uuid) STORED NOT NULL,
    value text NOT NULL
);


--
-- Name: pulse_values_id_seq; Type: SEQUENCE; Schema: mbi_filament; Owner: -
--

CREATE SEQUENCE mbi_filament.pulse_values_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: pulse_values_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_filament; Owner: -
--

ALTER SEQUENCE mbi_filament.pulse_values_id_seq OWNED BY mbi_filament.pulse_values.id;


--
-- Name: sessions; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: socialite_users; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.socialite_users (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    provider character varying(255) NOT NULL,
    provider_id character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: socialite_users_id_seq; Type: SEQUENCE; Schema: mbi_filament; Owner: -
--

CREATE SEQUENCE mbi_filament.socialite_users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: socialite_users_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_filament; Owner: -
--

ALTER SEQUENCE mbi_filament.socialite_users_id_seq OWNED BY mbi_filament.socialite_users.id;


--
-- Name: users; Type: TABLE; Schema: mbi_filament; Owner: -
--

CREATE TABLE mbi_filament.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    chatwoot_user_id integer
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: mbi_filament; Owner: -
--

CREATE SEQUENCE mbi_filament.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_filament; Owner: -
--

ALTER SEQUENCE mbi_filament.users_id_seq OWNED BY mbi_filament.users.id;


--
-- Name: link_entries; Type: TABLE; Schema: mbi_link_shortener; Owner: -
--

CREATE TABLE mbi_link_shortener.link_entries (
    data jsonb NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    id integer NOT NULL,
    shortened_link_id character varying(255)
);


--
-- Name: link_entries_id_seq; Type: SEQUENCE; Schema: mbi_link_shortener; Owner: -
--

CREATE SEQUENCE mbi_link_shortener.link_entries_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: link_entries_id_seq; Type: SEQUENCE OWNED BY; Schema: mbi_link_shortener; Owner: -
--

ALTER SEQUENCE mbi_link_shortener.link_entries_id_seq OWNED BY mbi_link_shortener.link_entries.id;


--
-- Name: shortened_links; Type: TABLE; Schema: mbi_link_shortener; Owner: -
--

CREATE TABLE mbi_link_shortener.shortened_links (
    id character varying(255) NOT NULL,
    base64_target_url text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    finalized_at timestamp(0) without time zone,
    metadata json,
    chatwoot_account_id character varying(255) GENERATED ALWAYS AS ((metadata ->> 'chatwoot_account_id'::text)) STORED,
    chatwoot_agent_id character varying(255) GENERATED ALWAYS AS ((metadata ->> 'chatwoot_agent_id'::text)) STORED,
    chatwoot_contact_id character varying(255) GENERATED ALWAYS AS ((metadata ->> 'chatwoot_contact_id'::text)) STORED,
    chatwoot_conversation_id character varying(255) GENERATED ALWAYS AS ((metadata ->> 'chatwoot_conversation_id'::text)) STORED
);


--
-- Name: customers; Type: TABLE; Schema: mbi_stripe; Owner: -
--

CREATE TABLE mbi_stripe.customers (
    data jsonb NOT NULL,
    created integer GENERATED ALWAYS AS (((data ->> 'created'::text))::integer) STORED NOT NULL,
    chatwoot_contact_id bigint GENERATED ALWAYS AS ((((data -> 'metadata'::text) ->> 'chatwoot_contact_id'::text))::integer) STORED,
    livemode boolean GENERATED ALWAYS AS (((data ->> 'livemode'::text))::boolean) STORED,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    id character varying(255) NOT NULL
);


--
-- Name: events; Type: TABLE; Schema: mbi_stripe; Owner: -
--

CREATE TABLE mbi_stripe.events (
    data jsonb NOT NULL,
    created integer GENERATED ALWAYS AS (((data ->> 'created'::text))::integer) STORED NOT NULL,
    object character varying(255) GENERATED ALWAYS AS ((((data -> 'data'::text) -> 'object'::text) ->> 'object'::text)) STORED NOT NULL,
    object_id character varying(255) GENERATED ALWAYS AS ((((data -> 'data'::text) -> 'object'::text) ->> 'id'::text)) STORED,
    livemode boolean GENERATED ALWAYS AS (((data ->> 'livemode'::text))::boolean) STORED,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    id character varying(255) NOT NULL
);


--
-- Name: invoices; Type: TABLE; Schema: mbi_stripe; Owner: -
--

CREATE TABLE mbi_stripe.invoices (
    data jsonb NOT NULL,
    created integer GENERATED ALWAYS AS (((data ->> 'created'::text))::integer) STORED NOT NULL,
    customer_id character varying(255) GENERATED ALWAYS AS ((data ->> 'customer'::text)) STORED,
    livemode boolean GENERATED ALWAYS AS (((data ->> 'livemode'::text))::boolean) STORED,
    currency character varying(255) GENERATED ALWAYS AS ((data ->> 'currency'::text)) STORED,
    status character varying(255) GENERATED ALWAYS AS ((data ->> 'status'::text)) STORED,
    paid boolean GENERATED ALWAYS AS (((data ->> 'paid'::text))::boolean) STORED,
    total integer GENERATED ALWAYS AS (((data ->> 'total'::text))::integer) STORED,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    id character varying(255) NOT NULL,
    base64_hosted_invoice_url character varying(255) GENERATED ALWAYS AS (encode(((data ->> 'hosted_invoice_url'::text))::bytea, 'base64'::text)) STORED,
    chatwoot_account_id integer GENERATED ALWAYS AS (((((data ->> 'metadata'::text))::jsonb ->> 'chatwoot_account_id'::text))::integer) STORED,
    chatwoot_conversation_id integer GENERATED ALWAYS AS (((((data ->> 'metadata'::text))::jsonb ->> 'chatwoot_conversation_id'::text))::integer) STORED,
    chatwoot_contact_id integer GENERATED ALWAYS AS (((((data ->> 'metadata'::text))::jsonb ->> 'chatwoot_contact_id'::text))::integer) STORED,
    chatwoot_agent_id integer GENERATED ALWAYS AS (((((data ->> 'metadata'::text))::jsonb ->> 'chatwoot_agent_id'::text))::integer) STORED
);


--
-- Name: prices; Type: TABLE; Schema: mbi_stripe; Owner: -
--

CREATE TABLE mbi_stripe.prices (
    data jsonb NOT NULL,
    created integer GENERATED ALWAYS AS (((data ->> 'created'::text))::integer) STORED NOT NULL,
    product_id character varying(255) GENERATED ALWAYS AS ((data ->> 'product'::text)) STORED,
    active boolean GENERATED ALWAYS AS (((data ->> 'active'::text))::boolean) STORED,
    currency character varying(255) GENERATED ALWAYS AS ((data ->> 'currency'::text)) STORED,
    type character varying(255) GENERATED ALWAYS AS ((data ->> 'type'::text)) STORED,
    livemode boolean GENERATED ALWAYS AS (((data ->> 'livemode'::text))::boolean) STORED,
    unit_amount integer GENERATED ALWAYS AS (((data ->> 'unit_amount'::text))::integer) STORED,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    id character varying(255) NOT NULL
);


--
-- Name: products; Type: TABLE; Schema: mbi_stripe; Owner: -
--

CREATE TABLE mbi_stripe.products (
    data jsonb NOT NULL,
    created integer GENERATED ALWAYS AS (((data ->> 'created'::text))::integer) STORED NOT NULL,
    name character varying(255) GENERATED ALWAYS AS ((data ->> 'name'::text)) STORED,
    description character varying(255) GENERATED ALWAYS AS ((data ->> 'description'::text)) STORED,
    default_price character varying(255) GENERATED ALWAYS AS ((data ->> 'default_price'::text)) STORED,
    active boolean GENERATED ALWAYS AS (((data ->> 'active'::text))::boolean) STORED,
    livemode boolean GENERATED ALWAYS AS (((data ->> 'livemode'::text))::boolean) STORED,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    id character varying(255) NOT NULL
);


--
-- Name: access_tokens id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.access_tokens ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.access_tokens_id_seq'::regclass);


--
-- Name: account_users id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.account_users ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.account_users_id_seq'::regclass);


--
-- Name: accounts id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.accounts ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.accounts_id_seq'::regclass);


--
-- Name: action_mailbox_inbound_emails id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.action_mailbox_inbound_emails ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.action_mailbox_inbound_emails_id_seq'::regclass);


--
-- Name: active_storage_attachments id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.active_storage_attachments ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.active_storage_attachments_id_seq'::regclass);


--
-- Name: active_storage_blobs id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.active_storage_blobs ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.active_storage_blobs_id_seq'::regclass);


--
-- Name: active_storage_variant_records id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.active_storage_variant_records ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.active_storage_variant_records_id_seq'::regclass);


--
-- Name: agent_bot_inboxes id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.agent_bot_inboxes ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.agent_bot_inboxes_id_seq'::regclass);


--
-- Name: agent_bots id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.agent_bots ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.agent_bots_id_seq'::regclass);


--
-- Name: applied_slas id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.applied_slas ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.applied_slas_id_seq'::regclass);


--
-- Name: articles id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.articles ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.articles_id_seq'::regclass);


--
-- Name: attachments id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.attachments ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.attachments_id_seq'::regclass);


--
-- Name: audits id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.audits ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.audits_id_seq'::regclass);


--
-- Name: automation_rules id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.automation_rules ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.automation_rules_id_seq'::regclass);


--
-- Name: campaigns id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.campaigns ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.campaigns_id_seq'::regclass);


--
-- Name: canned_responses id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.canned_responses ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.canned_responses_id_seq'::regclass);


--
-- Name: categories id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.categories ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.categories_id_seq'::regclass);


--
-- Name: channel_api id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_api ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.channel_api_id_seq'::regclass);


--
-- Name: channel_email id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_email ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.channel_email_id_seq'::regclass);


--
-- Name: channel_facebook_pages id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_facebook_pages ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.channel_facebook_pages_id_seq'::regclass);


--
-- Name: channel_line id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_line ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.channel_line_id_seq'::regclass);


--
-- Name: channel_sms id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_sms ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.channel_sms_id_seq'::regclass);


--
-- Name: channel_telegram id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_telegram ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.channel_telegram_id_seq'::regclass);


--
-- Name: channel_twilio_sms id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_twilio_sms ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.channel_twilio_sms_id_seq'::regclass);


--
-- Name: channel_twitter_profiles id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_twitter_profiles ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.channel_twitter_profiles_id_seq'::regclass);


--
-- Name: channel_web_widgets id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_web_widgets ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.channel_web_widgets_id_seq'::regclass);


--
-- Name: channel_whatsapp id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_whatsapp ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.channel_whatsapp_id_seq'::regclass);


--
-- Name: contact_inboxes id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.contact_inboxes ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.contact_inboxes_id_seq'::regclass);


--
-- Name: contacts id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.contacts ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.contacts_id_seq'::regclass);


--
-- Name: conversation_participants id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.conversation_participants ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.conversation_participants_id_seq'::regclass);


--
-- Name: conversations id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.conversations ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.conversations_id_seq'::regclass);


--
-- Name: csat_survey_responses id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.csat_survey_responses ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.csat_survey_responses_id_seq'::regclass);


--
-- Name: custom_attribute_definitions id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.custom_attribute_definitions ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.custom_attribute_definitions_id_seq'::regclass);


--
-- Name: custom_filters id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.custom_filters ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.custom_filters_id_seq'::regclass);


--
-- Name: dashboard_apps id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.dashboard_apps ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.dashboard_apps_id_seq'::regclass);


--
-- Name: data_imports id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.data_imports ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.data_imports_id_seq'::regclass);


--
-- Name: email_templates id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.email_templates ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.email_templates_id_seq'::regclass);


--
-- Name: folders id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.folders ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.folders_id_seq'::regclass);


--
-- Name: inbox_members id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.inbox_members ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.inbox_members_id_seq'::regclass);


--
-- Name: inboxes id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.inboxes ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.inboxes_id_seq'::regclass);


--
-- Name: installation_configs id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.installation_configs ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.installation_configs_id_seq'::regclass);


--
-- Name: integrations_hooks id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.integrations_hooks ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.integrations_hooks_id_seq'::regclass);


--
-- Name: labels id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.labels ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.labels_id_seq'::regclass);


--
-- Name: macros id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.macros ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.macros_id_seq'::regclass);


--
-- Name: mentions id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.mentions ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.mentions_id_seq'::regclass);


--
-- Name: messages id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.messages ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.messages_id_seq'::regclass);


--
-- Name: nc_evolutions id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.nc_evolutions ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.nc_evolutions_id_seq'::regclass);


--
-- Name: notes id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.notes ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.notes_id_seq'::regclass);


--
-- Name: notification_settings id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.notification_settings ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.notification_settings_id_seq'::regclass);


--
-- Name: notification_subscriptions id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.notification_subscriptions ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.notification_subscriptions_id_seq'::regclass);


--
-- Name: notifications id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.notifications ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.notifications_id_seq'::regclass);


--
-- Name: platform_app_permissibles id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.platform_app_permissibles ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.platform_app_permissibles_id_seq'::regclass);


--
-- Name: platform_apps id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.platform_apps ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.platform_apps_id_seq'::regclass);


--
-- Name: portal_members id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.portal_members ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.portal_members_id_seq'::regclass);


--
-- Name: portals id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.portals ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.portals_id_seq'::regclass);


--
-- Name: related_categories id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.related_categories ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.related_categories_id_seq'::regclass);


--
-- Name: reporting_events id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.reporting_events ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.reporting_events_id_seq'::regclass);


--
-- Name: sla_events id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.sla_events ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.sla_events_id_seq'::regclass);


--
-- Name: sla_policies id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.sla_policies ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.sla_policies_id_seq'::regclass);


--
-- Name: taggings id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.taggings ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.taggings_id_seq'::regclass);


--
-- Name: tags id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.tags ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.tags_id_seq'::regclass);


--
-- Name: team_members id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.team_members ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.team_members_id_seq'::regclass);


--
-- Name: teams id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.teams ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.teams_id_seq'::regclass);


--
-- Name: telegram_bots id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.telegram_bots ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.telegram_bots_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.users ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.users_id_seq'::regclass);


--
-- Name: webhooks id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.webhooks ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.webhooks_id_seq'::regclass);


--
-- Name: working_hours id; Type: DEFAULT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.working_hours ALTER COLUMN id SET DEFAULT nextval('mbi_chatwoot.working_hours_id_seq'::regclass);


--
-- Name: doctors id; Type: DEFAULT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.doctors ALTER COLUMN id SET DEFAULT nextval('mbi_filament.doctors_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.failed_jobs ALTER COLUMN id SET DEFAULT nextval('mbi_filament.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.jobs ALTER COLUMN id SET DEFAULT nextval('mbi_filament.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.migrations ALTER COLUMN id SET DEFAULT nextval('mbi_filament.migrations_id_seq'::regclass);


--
-- Name: patients id; Type: DEFAULT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.patients ALTER COLUMN id SET DEFAULT nextval('mbi_filament.patients_id_seq'::regclass);


--
-- Name: prescription_templates id; Type: DEFAULT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.prescription_templates ALTER COLUMN id SET DEFAULT nextval('mbi_filament.prescription_templates_id_seq'::regclass);


--
-- Name: pulse_aggregates id; Type: DEFAULT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.pulse_aggregates ALTER COLUMN id SET DEFAULT nextval('mbi_filament.pulse_aggregates_id_seq'::regclass);


--
-- Name: pulse_entries id; Type: DEFAULT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.pulse_entries ALTER COLUMN id SET DEFAULT nextval('mbi_filament.pulse_entries_id_seq'::regclass);


--
-- Name: pulse_values id; Type: DEFAULT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.pulse_values ALTER COLUMN id SET DEFAULT nextval('mbi_filament.pulse_values_id_seq'::regclass);


--
-- Name: socialite_users id; Type: DEFAULT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.socialite_users ALTER COLUMN id SET DEFAULT nextval('mbi_filament.socialite_users_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.users ALTER COLUMN id SET DEFAULT nextval('mbi_filament.users_id_seq'::regclass);


--
-- Name: link_entries id; Type: DEFAULT; Schema: mbi_link_shortener; Owner: -
--

ALTER TABLE ONLY mbi_link_shortener.link_entries ALTER COLUMN id SET DEFAULT nextval('mbi_link_shortener.link_entries_id_seq'::regclass);


--
-- Name: access_tokens access_tokens_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.access_tokens
    ADD CONSTRAINT access_tokens_pkey PRIMARY KEY (id);


--
-- Name: account_users account_users_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.account_users
    ADD CONSTRAINT account_users_pkey PRIMARY KEY (id);


--
-- Name: accounts accounts_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.accounts
    ADD CONSTRAINT accounts_pkey PRIMARY KEY (id);


--
-- Name: action_mailbox_inbound_emails action_mailbox_inbound_emails_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.action_mailbox_inbound_emails
    ADD CONSTRAINT action_mailbox_inbound_emails_pkey PRIMARY KEY (id);


--
-- Name: active_storage_attachments active_storage_attachments_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.active_storage_attachments
    ADD CONSTRAINT active_storage_attachments_pkey PRIMARY KEY (id);


--
-- Name: active_storage_blobs active_storage_blobs_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.active_storage_blobs
    ADD CONSTRAINT active_storage_blobs_pkey PRIMARY KEY (id);


--
-- Name: active_storage_variant_records active_storage_variant_records_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.active_storage_variant_records
    ADD CONSTRAINT active_storage_variant_records_pkey PRIMARY KEY (id);


--
-- Name: agent_bot_inboxes agent_bot_inboxes_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.agent_bot_inboxes
    ADD CONSTRAINT agent_bot_inboxes_pkey PRIMARY KEY (id);


--
-- Name: agent_bots agent_bots_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.agent_bots
    ADD CONSTRAINT agent_bots_pkey PRIMARY KEY (id);


--
-- Name: applied_slas applied_slas_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.applied_slas
    ADD CONSTRAINT applied_slas_pkey PRIMARY KEY (id);


--
-- Name: ar_internal_metadata ar_internal_metadata_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.ar_internal_metadata
    ADD CONSTRAINT ar_internal_metadata_pkey PRIMARY KEY (key);


--
-- Name: articles articles_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.articles
    ADD CONSTRAINT articles_pkey PRIMARY KEY (id);


--
-- Name: attachments attachments_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.attachments
    ADD CONSTRAINT attachments_pkey PRIMARY KEY (id);


--
-- Name: audits audits_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.audits
    ADD CONSTRAINT audits_pkey PRIMARY KEY (id);


--
-- Name: automation_rules automation_rules_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.automation_rules
    ADD CONSTRAINT automation_rules_pkey PRIMARY KEY (id);


--
-- Name: campaigns campaigns_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.campaigns
    ADD CONSTRAINT campaigns_pkey PRIMARY KEY (id);


--
-- Name: canned_responses canned_responses_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.canned_responses
    ADD CONSTRAINT canned_responses_pkey PRIMARY KEY (id);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: channel_api channel_api_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_api
    ADD CONSTRAINT channel_api_pkey PRIMARY KEY (id);


--
-- Name: channel_email channel_email_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_email
    ADD CONSTRAINT channel_email_pkey PRIMARY KEY (id);


--
-- Name: channel_facebook_pages channel_facebook_pages_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_facebook_pages
    ADD CONSTRAINT channel_facebook_pages_pkey PRIMARY KEY (id);


--
-- Name: channel_line channel_line_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_line
    ADD CONSTRAINT channel_line_pkey PRIMARY KEY (id);


--
-- Name: channel_sms channel_sms_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_sms
    ADD CONSTRAINT channel_sms_pkey PRIMARY KEY (id);


--
-- Name: channel_telegram channel_telegram_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_telegram
    ADD CONSTRAINT channel_telegram_pkey PRIMARY KEY (id);


--
-- Name: channel_twilio_sms channel_twilio_sms_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_twilio_sms
    ADD CONSTRAINT channel_twilio_sms_pkey PRIMARY KEY (id);


--
-- Name: channel_twitter_profiles channel_twitter_profiles_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_twitter_profiles
    ADD CONSTRAINT channel_twitter_profiles_pkey PRIMARY KEY (id);


--
-- Name: channel_web_widgets channel_web_widgets_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_web_widgets
    ADD CONSTRAINT channel_web_widgets_pkey PRIMARY KEY (id);


--
-- Name: channel_whatsapp channel_whatsapp_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.channel_whatsapp
    ADD CONSTRAINT channel_whatsapp_pkey PRIMARY KEY (id);


--
-- Name: contact_inboxes contact_inboxes_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.contact_inboxes
    ADD CONSTRAINT contact_inboxes_pkey PRIMARY KEY (id);


--
-- Name: contacts contacts_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.contacts
    ADD CONSTRAINT contacts_pkey PRIMARY KEY (id);


--
-- Name: conversation_participants conversation_participants_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.conversation_participants
    ADD CONSTRAINT conversation_participants_pkey PRIMARY KEY (id);


--
-- Name: conversations conversations_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.conversations
    ADD CONSTRAINT conversations_pkey PRIMARY KEY (id);


--
-- Name: csat_survey_responses csat_survey_responses_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.csat_survey_responses
    ADD CONSTRAINT csat_survey_responses_pkey PRIMARY KEY (id);


--
-- Name: custom_attribute_definitions custom_attribute_definitions_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.custom_attribute_definitions
    ADD CONSTRAINT custom_attribute_definitions_pkey PRIMARY KEY (id);


--
-- Name: custom_filters custom_filters_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.custom_filters
    ADD CONSTRAINT custom_filters_pkey PRIMARY KEY (id);


--
-- Name: dashboard_apps dashboard_apps_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.dashboard_apps
    ADD CONSTRAINT dashboard_apps_pkey PRIMARY KEY (id);


--
-- Name: data_imports data_imports_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.data_imports
    ADD CONSTRAINT data_imports_pkey PRIMARY KEY (id);


--
-- Name: email_templates email_templates_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.email_templates
    ADD CONSTRAINT email_templates_pkey PRIMARY KEY (id);


--
-- Name: folders folders_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.folders
    ADD CONSTRAINT folders_pkey PRIMARY KEY (id);


--
-- Name: inbox_members inbox_members_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.inbox_members
    ADD CONSTRAINT inbox_members_pkey PRIMARY KEY (id);


--
-- Name: inboxes inboxes_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.inboxes
    ADD CONSTRAINT inboxes_pkey PRIMARY KEY (id);


--
-- Name: installation_configs installation_configs_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.installation_configs
    ADD CONSTRAINT installation_configs_pkey PRIMARY KEY (id);


--
-- Name: integrations_hooks integrations_hooks_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.integrations_hooks
    ADD CONSTRAINT integrations_hooks_pkey PRIMARY KEY (id);


--
-- Name: labels labels_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.labels
    ADD CONSTRAINT labels_pkey PRIMARY KEY (id);


--
-- Name: macros macros_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.macros
    ADD CONSTRAINT macros_pkey PRIMARY KEY (id);


--
-- Name: mentions mentions_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.mentions
    ADD CONSTRAINT mentions_pkey PRIMARY KEY (id);


--
-- Name: messages messages_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (id);


--
-- Name: nc_evolutions nc_evolutions_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.nc_evolutions
    ADD CONSTRAINT nc_evolutions_pkey PRIMARY KEY (id);


--
-- Name: notes notes_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.notes
    ADD CONSTRAINT notes_pkey PRIMARY KEY (id);


--
-- Name: notification_settings notification_settings_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.notification_settings
    ADD CONSTRAINT notification_settings_pkey PRIMARY KEY (id);


--
-- Name: notification_subscriptions notification_subscriptions_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.notification_subscriptions
    ADD CONSTRAINT notification_subscriptions_pkey PRIMARY KEY (id);


--
-- Name: notifications notifications_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);


--
-- Name: platform_app_permissibles platform_app_permissibles_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.platform_app_permissibles
    ADD CONSTRAINT platform_app_permissibles_pkey PRIMARY KEY (id);


--
-- Name: platform_apps platform_apps_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.platform_apps
    ADD CONSTRAINT platform_apps_pkey PRIMARY KEY (id);


--
-- Name: portal_members portal_members_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.portal_members
    ADD CONSTRAINT portal_members_pkey PRIMARY KEY (id);


--
-- Name: portals portals_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.portals
    ADD CONSTRAINT portals_pkey PRIMARY KEY (id);


--
-- Name: related_categories related_categories_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.related_categories
    ADD CONSTRAINT related_categories_pkey PRIMARY KEY (id);


--
-- Name: reporting_events reporting_events_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.reporting_events
    ADD CONSTRAINT reporting_events_pkey PRIMARY KEY (id);


--
-- Name: schema_migrations schema_migrations_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.schema_migrations
    ADD CONSTRAINT schema_migrations_pkey PRIMARY KEY (version);


--
-- Name: sla_events sla_events_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.sla_events
    ADD CONSTRAINT sla_events_pkey PRIMARY KEY (id);


--
-- Name: sla_policies sla_policies_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.sla_policies
    ADD CONSTRAINT sla_policies_pkey PRIMARY KEY (id);


--
-- Name: taggings taggings_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.taggings
    ADD CONSTRAINT taggings_pkey PRIMARY KEY (id);


--
-- Name: tags tags_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.tags
    ADD CONSTRAINT tags_pkey PRIMARY KEY (id);


--
-- Name: team_members team_members_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.team_members
    ADD CONSTRAINT team_members_pkey PRIMARY KEY (id);


--
-- Name: teams teams_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.teams
    ADD CONSTRAINT teams_pkey PRIMARY KEY (id);


--
-- Name: telegram_bots telegram_bots_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.telegram_bots
    ADD CONSTRAINT telegram_bots_pkey PRIMARY KEY (id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: webhooks webhooks_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.webhooks
    ADD CONSTRAINT webhooks_pkey PRIMARY KEY (id);


--
-- Name: working_hours working_hours_pkey; Type: CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.working_hours
    ADD CONSTRAINT working_hours_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: chatwoot_contacts_patients chatwoot_contacts_patients_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.chatwoot_contacts_patients
    ADD CONSTRAINT chatwoot_contacts_patients_pkey PRIMARY KEY (chatwoot_contact_id, patient_id);


--
-- Name: doctors doctors_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.doctors
    ADD CONSTRAINT doctors_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs mbi_filament_failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.failed_jobs
    ADD CONSTRAINT mbi_filament_failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: users mbi_filament_users_email_unique; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.users
    ADD CONSTRAINT mbi_filament_users_email_unique UNIQUE (email);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: notifications notifications_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: patients patients_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.patients
    ADD CONSTRAINT patients_pkey PRIMARY KEY (id);


--
-- Name: prescription_templates prescription_templates_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.prescription_templates
    ADD CONSTRAINT prescription_templates_pkey PRIMARY KEY (id);


--
-- Name: pulse_aggregates pulse_aggregates_bucket_period_type_aggregate_key_hash_unique; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.pulse_aggregates
    ADD CONSTRAINT pulse_aggregates_bucket_period_type_aggregate_key_hash_unique UNIQUE (bucket, period, type, aggregate, key_hash);


--
-- Name: pulse_aggregates pulse_aggregates_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.pulse_aggregates
    ADD CONSTRAINT pulse_aggregates_pkey PRIMARY KEY (id);


--
-- Name: pulse_entries pulse_entries_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.pulse_entries
    ADD CONSTRAINT pulse_entries_pkey PRIMARY KEY (id);


--
-- Name: pulse_values pulse_values_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.pulse_values
    ADD CONSTRAINT pulse_values_pkey PRIMARY KEY (id);


--
-- Name: pulse_values pulse_values_type_key_hash_unique; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.pulse_values
    ADD CONSTRAINT pulse_values_type_key_hash_unique UNIQUE (type, key_hash);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: socialite_users socialite_users_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.socialite_users
    ADD CONSTRAINT socialite_users_pkey PRIMARY KEY (id);


--
-- Name: socialite_users socialite_users_provider_provider_id_unique; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.socialite_users
    ADD CONSTRAINT socialite_users_provider_provider_id_unique UNIQUE (provider, provider_id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: link_entries link_entries_pkey; Type: CONSTRAINT; Schema: mbi_link_shortener; Owner: -
--

ALTER TABLE ONLY mbi_link_shortener.link_entries
    ADD CONSTRAINT link_entries_pkey PRIMARY KEY (id);


--
-- Name: shortened_links shortened_links_pkey; Type: CONSTRAINT; Schema: mbi_link_shortener; Owner: -
--

ALTER TABLE ONLY mbi_link_shortener.shortened_links
    ADD CONSTRAINT shortened_links_pkey PRIMARY KEY (id);


--
-- Name: customers customers_pkey; Type: CONSTRAINT; Schema: mbi_stripe; Owner: -
--

ALTER TABLE ONLY mbi_stripe.customers
    ADD CONSTRAINT customers_pkey PRIMARY KEY (id);


--
-- Name: events events_pkey; Type: CONSTRAINT; Schema: mbi_stripe; Owner: -
--

ALTER TABLE ONLY mbi_stripe.events
    ADD CONSTRAINT events_pkey PRIMARY KEY (id);


--
-- Name: invoices invoices_pkey; Type: CONSTRAINT; Schema: mbi_stripe; Owner: -
--

ALTER TABLE ONLY mbi_stripe.invoices
    ADD CONSTRAINT invoices_pkey PRIMARY KEY (id);


--
-- Name: prices prices_pkey; Type: CONSTRAINT; Schema: mbi_stripe; Owner: -
--

ALTER TABLE ONLY mbi_stripe.prices
    ADD CONSTRAINT prices_pkey PRIMARY KEY (id);


--
-- Name: products products_pkey; Type: CONSTRAINT; Schema: mbi_stripe; Owner: -
--

ALTER TABLE ONLY mbi_stripe.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- Name: associated_index; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX associated_index ON mbi_chatwoot.audits USING btree (associated_type, associated_id);


--
-- Name: attribute_key_model_index; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX attribute_key_model_index ON mbi_chatwoot.custom_attribute_definitions USING btree (attribute_key, attribute_model, account_id);


--
-- Name: auditable_index; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX auditable_index ON mbi_chatwoot.audits USING btree (auditable_type, auditable_id, version);


--
-- Name: by_account_user; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX by_account_user ON mbi_chatwoot.notification_settings USING btree (account_id, user_id);


--
-- Name: conv_acid_inbid_stat_asgnid_idx; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX conv_acid_inbid_stat_asgnid_idx ON mbi_chatwoot.conversations USING btree (account_id, inbox_id, status, assignee_id);


--
-- Name: index_access_tokens_on_owner_type_and_owner_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_access_tokens_on_owner_type_and_owner_id ON mbi_chatwoot.access_tokens USING btree (owner_type, owner_id);


--
-- Name: index_access_tokens_on_token; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_access_tokens_on_token ON mbi_chatwoot.access_tokens USING btree (token);


--
-- Name: index_account_users_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_account_users_on_account_id ON mbi_chatwoot.account_users USING btree (account_id);


--
-- Name: index_account_users_on_user_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_account_users_on_user_id ON mbi_chatwoot.account_users USING btree (user_id);


--
-- Name: index_accounts_on_status; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_accounts_on_status ON mbi_chatwoot.accounts USING btree (status);


--
-- Name: index_action_mailbox_inbound_emails_uniqueness; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_action_mailbox_inbound_emails_uniqueness ON mbi_chatwoot.action_mailbox_inbound_emails USING btree (message_id, message_checksum);


--
-- Name: index_active_storage_attachments_on_blob_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_active_storage_attachments_on_blob_id ON mbi_chatwoot.active_storage_attachments USING btree (blob_id);


--
-- Name: index_active_storage_attachments_uniqueness; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_active_storage_attachments_uniqueness ON mbi_chatwoot.active_storage_attachments USING btree (record_type, record_id, name, blob_id);


--
-- Name: index_active_storage_blobs_on_key; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_active_storage_blobs_on_key ON mbi_chatwoot.active_storage_blobs USING btree (key);


--
-- Name: index_active_storage_variant_records_uniqueness; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_active_storage_variant_records_uniqueness ON mbi_chatwoot.active_storage_variant_records USING btree (blob_id, variation_digest);


--
-- Name: index_agent_bots_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_agent_bots_on_account_id ON mbi_chatwoot.agent_bots USING btree (account_id);


--
-- Name: index_applied_slas_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_applied_slas_on_account_id ON mbi_chatwoot.applied_slas USING btree (account_id);


--
-- Name: index_applied_slas_on_account_sla_policy_conversation; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_applied_slas_on_account_sla_policy_conversation ON mbi_chatwoot.applied_slas USING btree (account_id, sla_policy_id, conversation_id);


--
-- Name: index_applied_slas_on_conversation_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_applied_slas_on_conversation_id ON mbi_chatwoot.applied_slas USING btree (conversation_id);


--
-- Name: index_applied_slas_on_sla_policy_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_applied_slas_on_sla_policy_id ON mbi_chatwoot.applied_slas USING btree (sla_policy_id);


--
-- Name: index_articles_on_associated_article_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_articles_on_associated_article_id ON mbi_chatwoot.articles USING btree (associated_article_id);


--
-- Name: index_articles_on_author_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_articles_on_author_id ON mbi_chatwoot.articles USING btree (author_id);


--
-- Name: index_articles_on_slug; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_articles_on_slug ON mbi_chatwoot.articles USING btree (slug);


--
-- Name: index_attachments_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_attachments_on_account_id ON mbi_chatwoot.attachments USING btree (account_id);


--
-- Name: index_attachments_on_message_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_attachments_on_message_id ON mbi_chatwoot.attachments USING btree (message_id);


--
-- Name: index_audits_on_created_at; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_audits_on_created_at ON mbi_chatwoot.audits USING btree (created_at);


--
-- Name: index_audits_on_request_uuid; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_audits_on_request_uuid ON mbi_chatwoot.audits USING btree (request_uuid);


--
-- Name: index_automation_rules_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_automation_rules_on_account_id ON mbi_chatwoot.automation_rules USING btree (account_id);


--
-- Name: index_campaigns_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_campaigns_on_account_id ON mbi_chatwoot.campaigns USING btree (account_id);


--
-- Name: index_campaigns_on_campaign_status; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_campaigns_on_campaign_status ON mbi_chatwoot.campaigns USING btree (campaign_status);


--
-- Name: index_campaigns_on_campaign_type; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_campaigns_on_campaign_type ON mbi_chatwoot.campaigns USING btree (campaign_type);


--
-- Name: index_campaigns_on_inbox_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_campaigns_on_inbox_id ON mbi_chatwoot.campaigns USING btree (inbox_id);


--
-- Name: index_campaigns_on_scheduled_at; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_campaigns_on_scheduled_at ON mbi_chatwoot.campaigns USING btree (scheduled_at);


--
-- Name: index_categories_on_associated_category_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_categories_on_associated_category_id ON mbi_chatwoot.categories USING btree (associated_category_id);


--
-- Name: index_categories_on_locale; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_categories_on_locale ON mbi_chatwoot.categories USING btree (locale);


--
-- Name: index_categories_on_locale_and_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_categories_on_locale_and_account_id ON mbi_chatwoot.categories USING btree (locale, account_id);


--
-- Name: index_categories_on_parent_category_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_categories_on_parent_category_id ON mbi_chatwoot.categories USING btree (parent_category_id);


--
-- Name: index_categories_on_slug_and_locale_and_portal_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_categories_on_slug_and_locale_and_portal_id ON mbi_chatwoot.categories USING btree (slug, locale, portal_id);


--
-- Name: index_channel_api_on_hmac_token; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_channel_api_on_hmac_token ON mbi_chatwoot.channel_api USING btree (hmac_token);


--
-- Name: index_channel_api_on_identifier; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_channel_api_on_identifier ON mbi_chatwoot.channel_api USING btree (identifier);


--
-- Name: index_channel_email_on_email; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_channel_email_on_email ON mbi_chatwoot.channel_email USING btree (email);


--
-- Name: index_channel_email_on_forward_to_email; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_channel_email_on_forward_to_email ON mbi_chatwoot.channel_email USING btree (forward_to_email);


--
-- Name: index_channel_facebook_pages_on_page_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_channel_facebook_pages_on_page_id ON mbi_chatwoot.channel_facebook_pages USING btree (page_id);


--
-- Name: index_channel_facebook_pages_on_page_id_and_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_channel_facebook_pages_on_page_id_and_account_id ON mbi_chatwoot.channel_facebook_pages USING btree (page_id, account_id);


--
-- Name: index_channel_line_on_line_channel_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_channel_line_on_line_channel_id ON mbi_chatwoot.channel_line USING btree (line_channel_id);


--
-- Name: index_channel_sms_on_phone_number; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_channel_sms_on_phone_number ON mbi_chatwoot.channel_sms USING btree (phone_number);


--
-- Name: index_channel_telegram_on_bot_token; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_channel_telegram_on_bot_token ON mbi_chatwoot.channel_telegram USING btree (bot_token);


--
-- Name: index_channel_twilio_sms_on_account_sid_and_phone_number; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_channel_twilio_sms_on_account_sid_and_phone_number ON mbi_chatwoot.channel_twilio_sms USING btree (account_sid, phone_number);


--
-- Name: index_channel_twilio_sms_on_messaging_service_sid; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_channel_twilio_sms_on_messaging_service_sid ON mbi_chatwoot.channel_twilio_sms USING btree (messaging_service_sid);


--
-- Name: index_channel_twilio_sms_on_phone_number; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_channel_twilio_sms_on_phone_number ON mbi_chatwoot.channel_twilio_sms USING btree (phone_number);


--
-- Name: index_channel_twitter_profiles_on_account_id_and_profile_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_channel_twitter_profiles_on_account_id_and_profile_id ON mbi_chatwoot.channel_twitter_profiles USING btree (account_id, profile_id);


--
-- Name: index_channel_web_widgets_on_hmac_token; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_channel_web_widgets_on_hmac_token ON mbi_chatwoot.channel_web_widgets USING btree (hmac_token);


--
-- Name: index_channel_web_widgets_on_website_token; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_channel_web_widgets_on_website_token ON mbi_chatwoot.channel_web_widgets USING btree (website_token);


--
-- Name: index_channel_whatsapp_on_phone_number; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_channel_whatsapp_on_phone_number ON mbi_chatwoot.channel_whatsapp USING btree (phone_number);


--
-- Name: index_contact_inboxes_on_contact_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_contact_inboxes_on_contact_id ON mbi_chatwoot.contact_inboxes USING btree (contact_id);


--
-- Name: index_contact_inboxes_on_inbox_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_contact_inboxes_on_inbox_id ON mbi_chatwoot.contact_inboxes USING btree (inbox_id);


--
-- Name: index_contact_inboxes_on_inbox_id_and_source_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_contact_inboxes_on_inbox_id_and_source_id ON mbi_chatwoot.contact_inboxes USING btree (inbox_id, source_id);


--
-- Name: index_contact_inboxes_on_pubsub_token; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_contact_inboxes_on_pubsub_token ON mbi_chatwoot.contact_inboxes USING btree (pubsub_token);


--
-- Name: index_contact_inboxes_on_source_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_contact_inboxes_on_source_id ON mbi_chatwoot.contact_inboxes USING btree (source_id);


--
-- Name: index_contacts_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_contacts_on_account_id ON mbi_chatwoot.contacts USING btree (account_id);


--
-- Name: index_contacts_on_account_id_and_last_activity_at; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_contacts_on_account_id_and_last_activity_at ON mbi_chatwoot.contacts USING btree (account_id, last_activity_at DESC NULLS LAST);


--
-- Name: index_contacts_on_blocked; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_contacts_on_blocked ON mbi_chatwoot.contacts USING btree (blocked);


--
-- Name: index_contacts_on_lower_email_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_contacts_on_lower_email_account_id ON mbi_chatwoot.contacts USING btree (lower((email)::text), account_id);


--
-- Name: index_contacts_on_nonempty_fields; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_contacts_on_nonempty_fields ON mbi_chatwoot.contacts USING btree (account_id, email, phone_number, identifier) WHERE (((email)::text <> ''::text) OR ((phone_number)::text <> ''::text) OR ((identifier)::text <> ''::text));


--
-- Name: index_contacts_on_phone_number_and_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_contacts_on_phone_number_and_account_id ON mbi_chatwoot.contacts USING btree (phone_number, account_id);


--
-- Name: index_conversation_participants_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversation_participants_on_account_id ON mbi_chatwoot.conversation_participants USING btree (account_id);


--
-- Name: index_conversation_participants_on_conversation_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversation_participants_on_conversation_id ON mbi_chatwoot.conversation_participants USING btree (conversation_id);


--
-- Name: index_conversation_participants_on_user_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversation_participants_on_user_id ON mbi_chatwoot.conversation_participants USING btree (user_id);


--
-- Name: index_conversation_participants_on_user_id_and_conversation_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_conversation_participants_on_user_id_and_conversation_id ON mbi_chatwoot.conversation_participants USING btree (user_id, conversation_id);


--
-- Name: index_conversations_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversations_on_account_id ON mbi_chatwoot.conversations USING btree (account_id);


--
-- Name: index_conversations_on_account_id_and_display_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_conversations_on_account_id_and_display_id ON mbi_chatwoot.conversations USING btree (account_id, display_id);


--
-- Name: index_conversations_on_assignee_id_and_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversations_on_assignee_id_and_account_id ON mbi_chatwoot.conversations USING btree (assignee_id, account_id);


--
-- Name: index_conversations_on_campaign_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversations_on_campaign_id ON mbi_chatwoot.conversations USING btree (campaign_id);


--
-- Name: index_conversations_on_contact_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversations_on_contact_id ON mbi_chatwoot.conversations USING btree (contact_id);


--
-- Name: index_conversations_on_contact_inbox_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversations_on_contact_inbox_id ON mbi_chatwoot.conversations USING btree (contact_inbox_id);


--
-- Name: index_conversations_on_first_reply_created_at; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversations_on_first_reply_created_at ON mbi_chatwoot.conversations USING btree (first_reply_created_at);


--
-- Name: index_conversations_on_id_and_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversations_on_id_and_account_id ON mbi_chatwoot.conversations USING btree (account_id, id);


--
-- Name: index_conversations_on_inbox_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversations_on_inbox_id ON mbi_chatwoot.conversations USING btree (inbox_id);


--
-- Name: index_conversations_on_priority; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversations_on_priority ON mbi_chatwoot.conversations USING btree (priority);


--
-- Name: index_conversations_on_status_and_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversations_on_status_and_account_id ON mbi_chatwoot.conversations USING btree (status, account_id);


--
-- Name: index_conversations_on_status_and_priority; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversations_on_status_and_priority ON mbi_chatwoot.conversations USING btree (status, priority);


--
-- Name: index_conversations_on_team_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversations_on_team_id ON mbi_chatwoot.conversations USING btree (team_id);


--
-- Name: index_conversations_on_uuid; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_conversations_on_uuid ON mbi_chatwoot.conversations USING btree (uuid);


--
-- Name: index_conversations_on_waiting_since; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_conversations_on_waiting_since ON mbi_chatwoot.conversations USING btree (waiting_since);


--
-- Name: index_csat_survey_responses_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_csat_survey_responses_on_account_id ON mbi_chatwoot.csat_survey_responses USING btree (account_id);


--
-- Name: index_csat_survey_responses_on_assigned_agent_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_csat_survey_responses_on_assigned_agent_id ON mbi_chatwoot.csat_survey_responses USING btree (assigned_agent_id);


--
-- Name: index_csat_survey_responses_on_contact_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_csat_survey_responses_on_contact_id ON mbi_chatwoot.csat_survey_responses USING btree (contact_id);


--
-- Name: index_csat_survey_responses_on_conversation_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_csat_survey_responses_on_conversation_id ON mbi_chatwoot.csat_survey_responses USING btree (conversation_id);


--
-- Name: index_csat_survey_responses_on_message_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_csat_survey_responses_on_message_id ON mbi_chatwoot.csat_survey_responses USING btree (message_id);


--
-- Name: index_custom_attribute_definitions_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_custom_attribute_definitions_on_account_id ON mbi_chatwoot.custom_attribute_definitions USING btree (account_id);


--
-- Name: index_custom_filters_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_custom_filters_on_account_id ON mbi_chatwoot.custom_filters USING btree (account_id);


--
-- Name: index_custom_filters_on_user_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_custom_filters_on_user_id ON mbi_chatwoot.custom_filters USING btree (user_id);


--
-- Name: index_dashboard_apps_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_dashboard_apps_on_account_id ON mbi_chatwoot.dashboard_apps USING btree (account_id);


--
-- Name: index_dashboard_apps_on_user_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_dashboard_apps_on_user_id ON mbi_chatwoot.dashboard_apps USING btree (user_id);


--
-- Name: index_data_imports_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_data_imports_on_account_id ON mbi_chatwoot.data_imports USING btree (account_id);


--
-- Name: index_email_templates_on_name_and_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_email_templates_on_name_and_account_id ON mbi_chatwoot.email_templates USING btree (name, account_id);


--
-- Name: index_inbox_members_on_inbox_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_inbox_members_on_inbox_id ON mbi_chatwoot.inbox_members USING btree (inbox_id);


--
-- Name: index_inbox_members_on_inbox_id_and_user_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_inbox_members_on_inbox_id_and_user_id ON mbi_chatwoot.inbox_members USING btree (inbox_id, user_id);


--
-- Name: index_inboxes_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_inboxes_on_account_id ON mbi_chatwoot.inboxes USING btree (account_id);


--
-- Name: index_inboxes_on_channel_id_and_channel_type; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_inboxes_on_channel_id_and_channel_type ON mbi_chatwoot.inboxes USING btree (channel_id, channel_type);


--
-- Name: index_inboxes_on_portal_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_inboxes_on_portal_id ON mbi_chatwoot.inboxes USING btree (portal_id);


--
-- Name: index_installation_configs_on_name; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_installation_configs_on_name ON mbi_chatwoot.installation_configs USING btree (name);


--
-- Name: index_installation_configs_on_name_and_created_at; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_installation_configs_on_name_and_created_at ON mbi_chatwoot.installation_configs USING btree (name, created_at);


--
-- Name: index_labels_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_labels_on_account_id ON mbi_chatwoot.labels USING btree (account_id);


--
-- Name: index_labels_on_title_and_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_labels_on_title_and_account_id ON mbi_chatwoot.labels USING btree (title, account_id);


--
-- Name: index_macros_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_macros_on_account_id ON mbi_chatwoot.macros USING btree (account_id);


--
-- Name: index_mentions_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_mentions_on_account_id ON mbi_chatwoot.mentions USING btree (account_id);


--
-- Name: index_mentions_on_conversation_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_mentions_on_conversation_id ON mbi_chatwoot.mentions USING btree (conversation_id);


--
-- Name: index_mentions_on_user_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_mentions_on_user_id ON mbi_chatwoot.mentions USING btree (user_id);


--
-- Name: index_mentions_on_user_id_and_conversation_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_mentions_on_user_id_and_conversation_id ON mbi_chatwoot.mentions USING btree (user_id, conversation_id);


--
-- Name: index_messages_on_account_created_type; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_messages_on_account_created_type ON mbi_chatwoot.messages USING btree (account_id, created_at, message_type);


--
-- Name: index_messages_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_messages_on_account_id ON mbi_chatwoot.messages USING btree (account_id);


--
-- Name: index_messages_on_account_id_and_inbox_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_messages_on_account_id_and_inbox_id ON mbi_chatwoot.messages USING btree (account_id, inbox_id);


--
-- Name: index_messages_on_additional_attributes_campaign_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_messages_on_additional_attributes_campaign_id ON mbi_chatwoot.messages USING gin (((additional_attributes -> 'campaign_id'::text)));


--
-- Name: index_messages_on_conversation_account_type_created; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_messages_on_conversation_account_type_created ON mbi_chatwoot.messages USING btree (conversation_id, account_id, message_type, created_at);


--
-- Name: index_messages_on_conversation_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_messages_on_conversation_id ON mbi_chatwoot.messages USING btree (conversation_id);


--
-- Name: index_messages_on_created_at; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_messages_on_created_at ON mbi_chatwoot.messages USING btree (created_at);


--
-- Name: index_messages_on_inbox_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_messages_on_inbox_id ON mbi_chatwoot.messages USING btree (inbox_id);


--
-- Name: index_messages_on_sender_type_and_sender_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_messages_on_sender_type_and_sender_id ON mbi_chatwoot.messages USING btree (sender_type, sender_id);


--
-- Name: index_messages_on_source_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_messages_on_source_id ON mbi_chatwoot.messages USING btree (source_id);


--
-- Name: index_notes_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_notes_on_account_id ON mbi_chatwoot.notes USING btree (account_id);


--
-- Name: index_notes_on_contact_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_notes_on_contact_id ON mbi_chatwoot.notes USING btree (contact_id);


--
-- Name: index_notes_on_user_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_notes_on_user_id ON mbi_chatwoot.notes USING btree (user_id);


--
-- Name: index_notification_subscriptions_on_identifier; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_notification_subscriptions_on_identifier ON mbi_chatwoot.notification_subscriptions USING btree (identifier);


--
-- Name: index_notification_subscriptions_on_user_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_notification_subscriptions_on_user_id ON mbi_chatwoot.notification_subscriptions USING btree (user_id);


--
-- Name: index_notifications_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_notifications_on_account_id ON mbi_chatwoot.notifications USING btree (account_id);


--
-- Name: index_notifications_on_last_activity_at; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_notifications_on_last_activity_at ON mbi_chatwoot.notifications USING btree (last_activity_at);


--
-- Name: index_notifications_on_user_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_notifications_on_user_id ON mbi_chatwoot.notifications USING btree (user_id);


--
-- Name: index_platform_app_permissibles_on_permissibles; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_platform_app_permissibles_on_permissibles ON mbi_chatwoot.platform_app_permissibles USING btree (permissible_type, permissible_id);


--
-- Name: index_platform_app_permissibles_on_platform_app_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_platform_app_permissibles_on_platform_app_id ON mbi_chatwoot.platform_app_permissibles USING btree (platform_app_id);


--
-- Name: index_portal_members_on_portal_id_and_user_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_portal_members_on_portal_id_and_user_id ON mbi_chatwoot.portal_members USING btree (portal_id, user_id);


--
-- Name: index_portal_members_on_user_id_and_portal_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_portal_members_on_user_id_and_portal_id ON mbi_chatwoot.portal_members USING btree (user_id, portal_id);


--
-- Name: index_portals_members_on_portal_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_portals_members_on_portal_id ON mbi_chatwoot.portals_members USING btree (portal_id);


--
-- Name: index_portals_members_on_portal_id_and_user_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_portals_members_on_portal_id_and_user_id ON mbi_chatwoot.portals_members USING btree (portal_id, user_id);


--
-- Name: index_portals_members_on_user_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_portals_members_on_user_id ON mbi_chatwoot.portals_members USING btree (user_id);


--
-- Name: index_portals_on_channel_web_widget_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_portals_on_channel_web_widget_id ON mbi_chatwoot.portals USING btree (channel_web_widget_id);


--
-- Name: index_portals_on_custom_domain; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_portals_on_custom_domain ON mbi_chatwoot.portals USING btree (custom_domain);


--
-- Name: index_portals_on_slug; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_portals_on_slug ON mbi_chatwoot.portals USING btree (slug);


--
-- Name: index_related_categories_on_category_id_and_related_category_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_related_categories_on_category_id_and_related_category_id ON mbi_chatwoot.related_categories USING btree (category_id, related_category_id);


--
-- Name: index_related_categories_on_related_category_id_and_category_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_related_categories_on_related_category_id_and_category_id ON mbi_chatwoot.related_categories USING btree (related_category_id, category_id);


--
-- Name: index_reporting_events_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_reporting_events_on_account_id ON mbi_chatwoot.reporting_events USING btree (account_id);


--
-- Name: index_reporting_events_on_conversation_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_reporting_events_on_conversation_id ON mbi_chatwoot.reporting_events USING btree (conversation_id);


--
-- Name: index_reporting_events_on_created_at; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_reporting_events_on_created_at ON mbi_chatwoot.reporting_events USING btree (created_at);


--
-- Name: index_reporting_events_on_inbox_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_reporting_events_on_inbox_id ON mbi_chatwoot.reporting_events USING btree (inbox_id);


--
-- Name: index_reporting_events_on_name; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_reporting_events_on_name ON mbi_chatwoot.reporting_events USING btree (name);


--
-- Name: index_reporting_events_on_user_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_reporting_events_on_user_id ON mbi_chatwoot.reporting_events USING btree (user_id);


--
-- Name: index_resolved_contact_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_resolved_contact_account_id ON mbi_chatwoot.contacts USING btree (account_id) WHERE (((email)::text <> ''::text) OR ((phone_number)::text <> ''::text) OR ((identifier)::text <> ''::text));


--
-- Name: index_sla_events_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_sla_events_on_account_id ON mbi_chatwoot.sla_events USING btree (account_id);


--
-- Name: index_sla_events_on_applied_sla_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_sla_events_on_applied_sla_id ON mbi_chatwoot.sla_events USING btree (applied_sla_id);


--
-- Name: index_sla_events_on_conversation_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_sla_events_on_conversation_id ON mbi_chatwoot.sla_events USING btree (conversation_id);


--
-- Name: index_sla_events_on_inbox_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_sla_events_on_inbox_id ON mbi_chatwoot.sla_events USING btree (inbox_id);


--
-- Name: index_sla_events_on_sla_policy_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_sla_events_on_sla_policy_id ON mbi_chatwoot.sla_events USING btree (sla_policy_id);


--
-- Name: index_sla_policies_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_sla_policies_on_account_id ON mbi_chatwoot.sla_policies USING btree (account_id);


--
-- Name: index_taggings_on_context; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_taggings_on_context ON mbi_chatwoot.taggings USING btree (context);


--
-- Name: index_taggings_on_tag_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_taggings_on_tag_id ON mbi_chatwoot.taggings USING btree (tag_id);


--
-- Name: index_taggings_on_taggable_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_taggings_on_taggable_id ON mbi_chatwoot.taggings USING btree (taggable_id);


--
-- Name: index_taggings_on_taggable_id_and_taggable_type_and_context; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_taggings_on_taggable_id_and_taggable_type_and_context ON mbi_chatwoot.taggings USING btree (taggable_id, taggable_type, context);


--
-- Name: index_taggings_on_taggable_type; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_taggings_on_taggable_type ON mbi_chatwoot.taggings USING btree (taggable_type);


--
-- Name: index_taggings_on_tagger_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_taggings_on_tagger_id ON mbi_chatwoot.taggings USING btree (tagger_id);


--
-- Name: index_taggings_on_tagger_id_and_tagger_type; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_taggings_on_tagger_id_and_tagger_type ON mbi_chatwoot.taggings USING btree (tagger_id, tagger_type);


--
-- Name: index_tags_on_name; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_tags_on_name ON mbi_chatwoot.tags USING btree (name);


--
-- Name: index_team_members_on_team_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_team_members_on_team_id ON mbi_chatwoot.team_members USING btree (team_id);


--
-- Name: index_team_members_on_team_id_and_user_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_team_members_on_team_id_and_user_id ON mbi_chatwoot.team_members USING btree (team_id, user_id);


--
-- Name: index_team_members_on_user_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_team_members_on_user_id ON mbi_chatwoot.team_members USING btree (user_id);


--
-- Name: index_teams_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_teams_on_account_id ON mbi_chatwoot.teams USING btree (account_id);


--
-- Name: index_teams_on_name_and_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_teams_on_name_and_account_id ON mbi_chatwoot.teams USING btree (name, account_id);


--
-- Name: index_users_on_email; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_users_on_email ON mbi_chatwoot.users USING btree (email);


--
-- Name: index_users_on_pubsub_token; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_users_on_pubsub_token ON mbi_chatwoot.users USING btree (pubsub_token);


--
-- Name: index_users_on_reset_password_token; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_users_on_reset_password_token ON mbi_chatwoot.users USING btree (reset_password_token);


--
-- Name: index_users_on_uid_and_provider; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_users_on_uid_and_provider ON mbi_chatwoot.users USING btree (uid, provider);


--
-- Name: index_webhooks_on_account_id_and_url; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX index_webhooks_on_account_id_and_url ON mbi_chatwoot.webhooks USING btree (account_id, url);


--
-- Name: index_working_hours_on_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_working_hours_on_account_id ON mbi_chatwoot.working_hours USING btree (account_id);


--
-- Name: index_working_hours_on_inbox_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX index_working_hours_on_inbox_id ON mbi_chatwoot.working_hours USING btree (inbox_id);


--
-- Name: reporting_events__account_id__name__created_at; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX reporting_events__account_id__name__created_at ON mbi_chatwoot.reporting_events USING btree (account_id, name, created_at);


--
-- Name: taggings_idx; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX taggings_idx ON mbi_chatwoot.taggings USING btree (tag_id, taggable_id, taggable_type, context, tagger_id, tagger_type);


--
-- Name: taggings_idy; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX taggings_idy ON mbi_chatwoot.taggings USING btree (taggable_id, taggable_type, tagger_id, context);


--
-- Name: uniq_email_per_account_contact; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX uniq_email_per_account_contact ON mbi_chatwoot.contacts USING btree (account_id, email);


--
-- Name: uniq_identifier_per_account_contact; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX uniq_identifier_per_account_contact ON mbi_chatwoot.contacts USING btree (identifier, account_id);


--
-- Name: uniq_primary_actor_per_account_notifications; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX uniq_primary_actor_per_account_notifications ON mbi_chatwoot.notifications USING btree (primary_actor_type, primary_actor_id);


--
-- Name: uniq_secondary_actor_per_account_notifications; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX uniq_secondary_actor_per_account_notifications ON mbi_chatwoot.notifications USING btree (secondary_actor_type, secondary_actor_id);


--
-- Name: uniq_user_id_per_account_id; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX uniq_user_id_per_account_id ON mbi_chatwoot.account_users USING btree (account_id, user_id);


--
-- Name: unique_permissibles_index; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE UNIQUE INDEX unique_permissibles_index ON mbi_chatwoot.platform_app_permissibles USING btree (platform_app_id, permissible_id, permissible_type);


--
-- Name: user_index; Type: INDEX; Schema: mbi_chatwoot; Owner: -
--

CREATE INDEX user_index ON mbi_chatwoot.audits USING btree (user_id, user_type);


--
-- Name: mbi_filament_jobs_queue_index; Type: INDEX; Schema: mbi_filament; Owner: -
--

CREATE INDEX mbi_filament_jobs_queue_index ON mbi_filament.jobs USING btree (queue);


--
-- Name: mbi_filament_patients_created_by_index; Type: INDEX; Schema: mbi_filament; Owner: -
--

CREATE INDEX mbi_filament_patients_created_by_index ON mbi_filament.patients USING btree (created_by);


--
-- Name: mbi_filament_patients_updated_by_index; Type: INDEX; Schema: mbi_filament; Owner: -
--

CREATE INDEX mbi_filament_patients_updated_by_index ON mbi_filament.patients USING btree (updated_by);


--
-- Name: mbi_filament_sessions_last_activity_index; Type: INDEX; Schema: mbi_filament; Owner: -
--

CREATE INDEX mbi_filament_sessions_last_activity_index ON mbi_filament.sessions USING btree (last_activity);


--
-- Name: mbi_filament_sessions_user_id_index; Type: INDEX; Schema: mbi_filament; Owner: -
--

CREATE INDEX mbi_filament_sessions_user_id_index ON mbi_filament.sessions USING btree (user_id);


--
-- Name: notifications_notifiable_type_notifiable_id_index; Type: INDEX; Schema: mbi_filament; Owner: -
--

CREATE INDEX notifications_notifiable_type_notifiable_id_index ON mbi_filament.notifications USING btree (notifiable_type, notifiable_id);


--
-- Name: pulse_aggregates_period_bucket_index; Type: INDEX; Schema: mbi_filament; Owner: -
--

CREATE INDEX pulse_aggregates_period_bucket_index ON mbi_filament.pulse_aggregates USING btree (period, bucket);


--
-- Name: pulse_aggregates_period_type_aggregate_bucket_index; Type: INDEX; Schema: mbi_filament; Owner: -
--

CREATE INDEX pulse_aggregates_period_type_aggregate_bucket_index ON mbi_filament.pulse_aggregates USING btree (period, type, aggregate, bucket);


--
-- Name: pulse_aggregates_type_index; Type: INDEX; Schema: mbi_filament; Owner: -
--

CREATE INDEX pulse_aggregates_type_index ON mbi_filament.pulse_aggregates USING btree (type);


--
-- Name: pulse_entries_key_hash_index; Type: INDEX; Schema: mbi_filament; Owner: -
--

CREATE INDEX pulse_entries_key_hash_index ON mbi_filament.pulse_entries USING btree (key_hash);


--
-- Name: pulse_entries_timestamp_index; Type: INDEX; Schema: mbi_filament; Owner: -
--

CREATE INDEX pulse_entries_timestamp_index ON mbi_filament.pulse_entries USING btree ("timestamp");


--
-- Name: pulse_entries_timestamp_type_key_hash_value_index; Type: INDEX; Schema: mbi_filament; Owner: -
--

CREATE INDEX pulse_entries_timestamp_type_key_hash_value_index ON mbi_filament.pulse_entries USING btree ("timestamp", type, key_hash, value);


--
-- Name: pulse_entries_type_index; Type: INDEX; Schema: mbi_filament; Owner: -
--

CREATE INDEX pulse_entries_type_index ON mbi_filament.pulse_entries USING btree (type);


--
-- Name: pulse_values_timestamp_index; Type: INDEX; Schema: mbi_filament; Owner: -
--

CREATE INDEX pulse_values_timestamp_index ON mbi_filament.pulse_values USING btree ("timestamp");


--
-- Name: pulse_values_type_index; Type: INDEX; Schema: mbi_filament; Owner: -
--

CREATE INDEX pulse_values_type_index ON mbi_filament.pulse_values USING btree (type);


--
-- Name: accounts accounts_after_insert_row_tr; Type: TRIGGER; Schema: mbi_chatwoot; Owner: -
--

CREATE TRIGGER accounts_after_insert_row_tr AFTER INSERT ON mbi_chatwoot.accounts FOR EACH ROW EXECUTE FUNCTION mbi_chatwoot.accounts_after_insert_row_tr();


--
-- Name: accounts camp_dpid_before_insert; Type: TRIGGER; Schema: mbi_chatwoot; Owner: -
--

CREATE TRIGGER camp_dpid_before_insert AFTER INSERT ON mbi_chatwoot.accounts FOR EACH ROW EXECUTE FUNCTION mbi_chatwoot.camp_dpid_before_insert();


--
-- Name: campaigns campaigns_before_insert_row_tr; Type: TRIGGER; Schema: mbi_chatwoot; Owner: -
--

CREATE TRIGGER campaigns_before_insert_row_tr BEFORE INSERT ON mbi_chatwoot.campaigns FOR EACH ROW EXECUTE FUNCTION mbi_chatwoot.campaigns_before_insert_row_tr();


--
-- Name: conversations conversations_before_insert_row_tr; Type: TRIGGER; Schema: mbi_chatwoot; Owner: -
--

CREATE TRIGGER conversations_before_insert_row_tr BEFORE INSERT ON mbi_chatwoot.conversations FOR EACH ROW EXECUTE FUNCTION mbi_chatwoot.conversations_before_insert_row_tr();


--
-- Name: contacts n8n_trigger_4bdf0478_5c90_4354_95bb_899655c3715d; Type: TRIGGER; Schema: mbi_chatwoot; Owner: -
--

CREATE TRIGGER n8n_trigger_4bdf0478_5c90_4354_95bb_899655c3715d AFTER UPDATE ON mbi_chatwoot.contacts FOR EACH ROW EXECUTE FUNCTION mbi_chatwoot.n8n_trigger_function_4bdf0478_5c90_4354_95bb_899655c3715d();


--
-- Name: contacts n8n_trigger_85defb4f_70f7_45ce_bb57_06dd774d85ba; Type: TRIGGER; Schema: mbi_chatwoot; Owner: -
--

CREATE TRIGGER n8n_trigger_85defb4f_70f7_45ce_bb57_06dd774d85ba AFTER INSERT ON mbi_chatwoot.contacts FOR EACH ROW EXECUTE FUNCTION mbi_chatwoot.n8n_trigger_function_85defb4f_70f7_45ce_bb57_06dd774d85ba();


--
-- Name: active_storage_variant_records fk_rails_993965df05; Type: FK CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.active_storage_variant_records
    ADD CONSTRAINT fk_rails_993965df05 FOREIGN KEY (blob_id) REFERENCES mbi_chatwoot.active_storage_blobs(id);


--
-- Name: inboxes fk_rails_a1f654bf2d; Type: FK CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.inboxes
    ADD CONSTRAINT fk_rails_a1f654bf2d FOREIGN KEY (portal_id) REFERENCES mbi_chatwoot.portals(id);


--
-- Name: active_storage_attachments fk_rails_c3b3935057; Type: FK CONSTRAINT; Schema: mbi_chatwoot; Owner: -
--

ALTER TABLE ONLY mbi_chatwoot.active_storage_attachments
    ADD CONSTRAINT fk_rails_c3b3935057 FOREIGN KEY (blob_id) REFERENCES mbi_chatwoot.active_storage_blobs(id);


--
-- Name: chatwoot_contacts_patients mbi_filament_chatwoot_contacts_patients_patient_id_foreign; Type: FK CONSTRAINT; Schema: mbi_filament; Owner: -
--

ALTER TABLE ONLY mbi_filament.chatwoot_contacts_patients
    ADD CONSTRAINT mbi_filament_chatwoot_contacts_patients_patient_id_foreign FOREIGN KEY (patient_id) REFERENCES mbi_filament.patients(id);


--
-- PostgreSQL database dump complete
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 15.7
-- Dumped by pg_dump version 15.6 (Debian 15.6-0+deb12u1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: migrations; Type: TABLE DATA; Schema: mbi_filament; Owner: -
--

COPY mbi_filament.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2024_04_22_215214_create_patients_table	2
5	2024_04_23_071444_create_chatwoot_contacts_patients_table	3
248	2024_05_26_012651_drop_stripe_objects_table	16
249	2024_05_26_012702_drop_stripe_events_table	16
250	2024_05_26_012915_drop_process_event_insertion_function	16
251	2024_05_26_015101_update_stripe_tables	17
252	2024_05_26_094856_update_stripe_tables_and_create_events_table	18
124	2024_04_26_225222_create_prescription_templates_table	4
125	2024_04_26_230347_create_doctors_table	4
257	2024_05_26_101642_update_mbi_stripe_events_table	19
258	2024_05_26_113927_update_mbi_stripe_customers_table	20
259	2024_05_28_004539_add_active_since_to_stripe_prices_table	21
260	2024_05_30_145417_update_id_columns	22
261	2024_05_30_152656_update_id_column_in_events_table	23
264	2024_05_31_195034_add_columns_to_stripe_tables	24
265	2024_06_01_003053_add_unit_amount_collumn_to_prices_table	25
266	2024_06_05_233051_create_socialite_users_table	26
267	2024_06_07_195547_create_pulse_tables	27
268	2024_06_09_134302_add_timestamps_to_mbi_stripe_tables	28
269	2024_06_09_154547_update_mbi_stripe_tables	29
270	2024_06_10_012534_create_notifications_table	30
271	2024_06_10_222759_create_shortened_links_table	31
272	2024_06_10_225312_add_kv_created_at_to_shortened_links_table	32
273	2024_06_10_225524_add_kv_expired_at_to_shortened_links_table	33
208	2024_05_01_091540_create_stripe_events_table	5
209	2024_05_01_091541_create_stripe_objects_table	5
210	2024_05_01_102958_add_new_columns_to_stripe_tables	5
211	2024_05_01_103622_create_function_and_trigger_for_event_insertion	5
212	2024_05_03_091300_add_created_column_to_stripe_objects_table	6
279	2024_06_10_225312_add_finalized_at_and_kv_expires_at_to_shortened_links_table	34
280	2024_06_11_224358_update_shortened_links_table	35
215	2024_05_03_091729_update_stripe_data_handling	7
282	2024_06_11_230458_add_metadata_to_shortened_links_table	36
283	2024_06_11_230340_update_metadata_to_json_in_shortened_links_table	37
284	2024_06_12_202148_add_base64_hosted_invoice_url_to_stripe_invoices_table	38
285	2024_06_12_203547_rename_base64_encoded_target_url_to_base64_target_url_in_shortened_links_table	39
222	2024_05_03_190633_chatwoot_contact_stripe_customer	8
288	2024_06_12_213941_create_link_entries_table	40
289	2024_06_13_000558_add_shortened_link_id_to_link_entries_table	40
227	2024_05_04_125025_add_indexes_to_stripe_objects_table	9
294	2024_06_13_113932_modify_link_entries_table	41
228	2024_05_04_125635_add_indexes_to_stripe_events_table	9
229	2024_05_04_130401_add_json_indexes_to_stripe_objects_table	9
230	2024_05_09_113436_create_socialite_users_table	10
231	2024_05_09_115803_make_user_password_nullable	11
232	2024_05_09_145100_add_indexes_to_user_table	12
233	2024_05_09_165112_create_notifications_table	13
234	2024_05_16_123437_create_customers_table	14
235	2024_05_16_123514_create_products_table	14
236	2024_05_16_123529_create_prices_table	14
237	2024_05_16_123549_create_invoices_table	14
295	2024_06_18_155100_modify_shortened_links_table	42
296	2024_06_22_175905_add_chattoot_user_id_to_users_table	43
297	2024_06_24_192913_add_chatwoot_columns_to_invoices_table	44
243	2024_05_16_214332_add_chatwoot_contact_id_to_stripe_customers_table	15
\.


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: mbi_filament; Owner: -
--

SELECT pg_catalog.setval('mbi_filament.migrations_id_seq', 297, true);


--
-- PostgreSQL database dump complete
--

